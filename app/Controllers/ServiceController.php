<?php
/**
 * ServiceController — Cadastro, edição e exclusão de serviços
 *
 * Fluxo:
 * - create / store  → novo serviço (status Pendente)
 * - edit / update   → alterar descrição e preço
 * - delete          → remover serviço
 * - finish          → data de finalização + comissão + e-mail
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Service;
use App\Models\User;

class ServiceController extends Controller
{
    /** @var Service */
    private $serviceModel;

    /** @var User */
    private $userModel;

    public function __construct()
    {
        $this->serviceModel = new Service();
        $this->userModel    = new User();
    }

    /**
     * Exibe o formulário de cadastro de serviço.
     */
    public function create(): void
    {
        $this->requireAuth();

        $this->render('service/create', [
            'user'       => $_SESSION['user'],
            'csrf_token' => $this->generateCsrfToken(),
            'description'=> '',
            'price'      => '',
            'error'      => null,
        ]);
    }

    /**
     * Processa o cadastro de um novo serviço.
     * Sucesso ou falha → flash + redireciona ao dashboard.
     */
    public function store(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('service/create');
        }

        if (!$this->csrfIsValid()) {
            $this->setFlash('error', 'Falha ao cadastrar o serviço.');
            $this->redirect('dashboard');
        }

        $description = trim($_POST['description'] ?? '');
        $priceRaw    = trim($_POST['price'] ?? '');

        $error = $this->validateServiceInput($description, $priceRaw);

        if ($error !== null) {
            $this->setFlash('error', 'Falha ao cadastrar o serviço.');
            $this->redirect('dashboard');
        }

        $price  = $this->parsePrice($priceRaw);
        $userId = (int) $_SESSION['user']['id_user'];

        try {
            $created = $this->serviceModel->create($description, $price, $userId);
        } catch (\Exception $e) {
            $created = false;
        }

        if ($created) {
            $this->setFlash('success', 'Serviço cadastrado com sucesso!');
        } else {
            $this->setFlash('error', 'Falha ao cadastrar o serviço.');
        }

        $this->redirect('dashboard');
    }

    /**
     * Exibe o formulário de edição de serviço.
     */
    public function edit(): void
    {
        $this->requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        $service = $this->serviceModel->findById($id);

        if (!$service) {
            $this->setFlash('error', 'Serviço não encontrado.');
            $this->redirect('dashboard');
        }

        $this->render('service/edit', [
            'user'       => $_SESSION['user'],
            'csrf_token' => $this->generateCsrfToken(),
            'service'    => $service,
            'error'      => null,
        ]);
    }

    /**
     * Processa a atualização de um serviço.
     */
    public function update(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboard');
        }

        $this->validateCsrf();

        $id          = (int) ($_POST['id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $priceRaw    = trim($_POST['price'] ?? '');

        $service = $this->serviceModel->findById($id);
        if (!$service) {
            $this->setFlash('error', 'Serviço não encontrado.');
            $this->redirect('dashboard');
        }

        $error = $this->validateServiceInput($description, $priceRaw);

        if ($error !== null) {
            $this->setFlash('error', $error);
            $this->redirect('dashboard');
        }

        $price = $this->parsePrice($priceRaw);

        if ($this->serviceModel->update($id, $description, $price)) {
            $this->setFlash('success', 'Serviço atualizado com sucesso!');
        } else {
            $this->setFlash('error', 'Falha ao atualizar o serviço. Tente novamente.');
        }

        $this->redirect('dashboard');
    }

    /**
     * Exclui um serviço.
     */
    public function delete(): void
    {
        $this->requireAuth();

        $id = (int) ($_GET['id'] ?? 0);
        $service = $this->serviceModel->findById($id);

        if (!$service) {
            $this->setFlash('error', 'Serviço não encontrado.');
            $this->redirect('dashboard');
        }

        if ($this->serviceModel->delete($id)) {
            $this->setFlash('success', 'Serviço excluído com sucesso!');
        } else {
            $this->setFlash('error', 'Falha ao excluir o serviço. Tente novamente.');
        }

        $this->redirect('dashboard');
    }

    /**
     * Finaliza um serviço pendente: grava data, calcula comissão
     * e tenta enviar e-mail para o usuário dono do serviço.
     *
     * Aceita GET (link) ou POST AJAX (jQuery).
     */
    public function finish(): void
    {
        $this->requireAuth();

        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $isAjax = $this->isAjaxRequest();

        $service = $this->serviceModel->findById($id);

        if (!$service) {
            $this->respondFinish(false, 'Serviço não encontrado.', $isAjax);
        }

        if ($service['finished_at'] !== null) {
            $this->respondFinish(false, 'Este serviço já está finalizado.', $isAjax);
        }

        if (!$this->serviceModel->finish($id)) {
            $this->respondFinish(false, 'Falha ao finalizar o serviço. Tente novamente.', $isAjax);
        }

        $updated    = $this->serviceModel->findById($id);
        $commission = (float) $updated['commission_user'];
        $this->notifyOwnerByEmail($service, $commission);

        $message = 'Serviço finalizado. Comissão: R$ ' . number_format($commission, 2, ',', '.');
        $this->respondFinish(true, $message, $isAjax);
    }

    /**
     * Responde à finalização: JSON (AJAX) ou flash + redirect (navegação normal).
     *
     * @param bool   $success
     * @param string $message
     * @param bool   $isAjax
     */
    private function respondFinish($success, $message, $isAjax)
    {
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => $success,
                'message' => $message,
            ]);
            exit;
        }

        $this->setFlash($success ? 'success' : 'error', $message);
        $this->redirect('dashboard');
    }

    /**
     * Detecta se a requisição veio via AJAX (jQuery).
     *
     * @return bool
     */
    private function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Envia e-mail ao dono do serviço.
     * No XAMPP o mail() costuma falhar — o serviço já foi gravado mesmo assim.
     *
     * @param array $service
     * @param float $commission
     */
    private function notifyOwnerByEmail(array $service, $commission)
    {
        $owner = $this->userModel->findById($service['user_id_user']);

        if (!$owner || empty($owner['email'])) {
            return;
        }

        $priceFormatted      = number_format((float) $service['price'], 2, ',', '.');
        $commissionFormatted = number_format($commission, 2, ',', '.');

        $subject = 'Serviço finalizado — ' . $service['description'];
        $body    = "Olá, {$owner['name']}\n\n"
            . "O serviço \"{$service['description']}\" foi finalizado.\n"
            . "Valor: R$ {$priceFormatted}\n"
            . "Comissão: R$ {$commissionFormatted}\n";

        $headers = "From: Sistema JM <sistema@localhost>\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n";

        @mail($owner['email'], '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
    }

    /**
     * Valida descrição e preço.
     * Retorna mensagem de erro ou null se estiver ok.
     *
     * @param string $description
     * @param string $priceRaw
     * @return string|null
     */
    private function validateServiceInput($description, $priceRaw)
    {
        if ($description === '' || $priceRaw === '') {
            return 'Falha ao cadastrar: informe a descrição e o valor do serviço.';
        }

        if (function_exists('mb_strlen')) {
            $length = mb_strlen($description);
        } else {
            $length = strlen($description);
        }

        if ($length > 45) {
            return 'Falha ao cadastrar: a descrição deve ter no máximo 45 caracteres.';
        }

        $price = $this->parsePrice($priceRaw);

        if ($price === null || $price <= 0) {
            return 'Falha ao cadastrar: informe um valor numérico válido maior que zero.';
        }

        return null;
    }

    /**
     * Converte preço digitado (aceita 100,50 ou 100.50) para float.
     *
     * @param string $priceRaw
     * @return float|null
     */
    private function parsePrice($priceRaw)
    {
        // Remove espaços e troca vírgula por ponto
        $normalized = str_replace([' ', ','], ['', '.'], $priceRaw);

        // Se tiver mais de um ponto (ex: 1.000.50), remove pontos de milhar
        if (substr_count($normalized, '.') > 1) {
            $parts = explode('.', $normalized);
            $decimal = array_pop($parts);
            $normalized = implode('', $parts) . '.' . $decimal;
        }

        if (!is_numeric($normalized)) {
            return null;
        }

        return (float) $normalized;
    }
}
