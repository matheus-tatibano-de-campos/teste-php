<?php
/**
 * ServiceController — Cadastro, edição e exclusão de serviços
 *
 * Fluxo:
 * - create / store  → novo serviço (status Pendente)
 * - edit / update   → alterar descrição e preço
 * - delete          → remover serviço
 * - finish          → será implementado na Camada 6
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    /** @var Service */
    private $serviceModel;

    public function __construct()
    {
        $this->serviceModel = new Service();
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

        $this->validateCsrf();

        $description = trim($_POST['description'] ?? '');
        $priceRaw    = trim($_POST['price'] ?? '');

        $error = $this->validateServiceInput($description, $priceRaw);

        if ($error !== null) {
            $this->setFlash('error', $error);
            $this->redirect('dashboard');
        }

        $price  = $this->parsePrice($priceRaw);
        $userId = (int) $_SESSION['user']['id_user'];

        if ($this->serviceModel->create($description, $price, $userId)) {
            $this->setFlash('success', 'Serviço cadastrado com sucesso!');
        } else {
            $this->setFlash('error', 'Falha ao cadastrar o serviço. Tente novamente.');
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
     * Placeholder — finalização será feita na Camada 6.
     */
    public function finish(): void
    {
        $this->requireAuth();
        $this->setFlash('error', 'Finalizar serviço será implementado na próxima camada.');
        $this->redirect('dashboard');
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

        if (mb_strlen($description) > 45) {
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
