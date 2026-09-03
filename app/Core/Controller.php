<?php
/**
 * Classe Controller — Base para todos os Controllers
 *
 * Fornece métodos de renderização de views, redirecionamento,
 * manipulação de sessão e proteção de rotas autenticadas.
 */

namespace App\Core;

abstract class Controller
{
    /**
     * Renderiza uma view passando variáveis para ela.
     *
     * Como funciona:
     * - extract() transforma o array $data em variáveis locais
     *   ex: ['usuario' => 'José'] vira $usuario = 'José' na view
     * - require inclui o arquivo PHP da view no escopo atual
     *
     * @param string $view  Caminho relativo dentro de app/Views/
     *                      Exemplo: 'auth/login' carrega app/Views/auth/login.php
     * @param array  $data  Variáveis a serem disponibilizadas na view
     */
    protected function render(string $view, array $data = []): void
    {
        // Extrai o array como variáveis individuais
        extract($data);

        $viewPath = __DIR__ . '/../Views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            die("View não encontrada: {$view}");
        }

        require $viewPath;
    }

    /**
     * Redireciona o navegador para outra rota do sistema.
     *
     * Usa o parâmetro ?route= porque o Apache (XAMPP) não tem rewrite
     * automático. Exemplo: redirect('dashboard') → .../index.php?route=dashboard
     *
     * @param string $path Nome da rota (ex: 'dashboard', 'auth/login')
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . '?route=' . $path);
        exit;
    }

    /**
     * Protege rotas que exigem autenticação.
     * Se o usuário não estiver logado, redireciona para o login.
     */
    protected function requireAuth(): void
    {
        if (empty($_SESSION['user'])) {
            $this->redirect('auth/login');
        }
    }

    /**
     * Salva uma mensagem flash na sessão.
     * A mensagem é exibida uma vez e depois apagada.
     *
     * @param string $type    Tipo: 'success' ou 'error'
     * @param string $message Texto da mensagem
     */
    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Retorna e remove a mensagem flash da sessão.
     * Retorna null se não houver mensagem.
     *
     * @return array|null
     */
    protected function getFlash(): ?array
    {
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * Gera um token CSRF e o armazena na sessão.
     * Deve ser chamado ao renderizar formulários de mutação.
     *
     * @return string Token para colocar em campo hidden do form
     */
    protected function generateCsrfToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    /**
     * Valida o token CSRF enviado pelo formulário.
     * Encerra a execução com erro 403 se inválido.
     */
    protected function validateCsrf(): void
    {
        if (!$this->csrfIsValid()) {
            http_response_code(403);
            die('Requisição inválida. Tente novamente.');
        }
    }

    /**
     * Verifica o token CSRF sem encerrar a página.
     * Usado no cadastro de serviço para redirecionar com mensagem de falha.
     *
     * @return bool
     */
    protected function csrfIsValid()
    {
        $token = $_POST['csrf_token'] ?? '';

        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }

        unset($_SESSION['csrf_token']);
        return true;
    }
}
