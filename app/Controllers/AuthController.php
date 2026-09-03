<?php
/**
 * AuthController — Login, logout e cadastro de usuários
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Tela de login.
     * GET  → exibe o formulário
     * POST → valida credenciais e redireciona ao dashboard
     */
    public function login(): void
    {
        // Se já estiver logado, vai direto ao dashboard
        if (!empty($_SESSION['user'])) {
            $this->redirect('dashboard');
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByEmail($email);

            // Valida e-mail e senha com password_verify
            if ($user && password_verify($password, $user['password'])) {
                // Regenera ID da sessão para evitar fixação de sessão
                session_regenerate_id(true);

                // Guarda apenas dados necessários na sessão (nunca a senha)
                $_SESSION['user'] = [
                    'id_user' => $user['id_user'],
                    'name'    => $user['name'],
                    'email'   => $user['email'],
                ];

                $this->redirect('dashboard');
            }

            // Mensagem exata exigida pelo requisito
            $error = 'Ops, Email ou Senha inválido';
        }

        $this->render('auth/login', [
            'error' => $error,
            'email' => $_POST['email'] ?? '',
            'flash' => $this->getFlash(),
        ]);
    }

    /**
     * Tela de cadastro de usuário.
     * GET  → exibe o formulário
     * POST → cria o usuário no banco
     */
    public function register(): void
    {
        if (!empty($_SESSION['user'])) {
            $this->redirect('dashboard');
        }

        $error   = null;
        $success = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();

            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($email === '' || $password === '') {
                $error = 'Informe e-mail e senha.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'E-mail inválido.';
            } elseif ($this->userModel->emailExists($email)) {
                $error = 'Este e-mail já está cadastrado.';
            } else {
                // Wireframe não tem campo nome — usa parte antes do @ como nome
                $name = explode('@', $email)[0];

                if ($this->userModel->create($name, $email, $password)) {
                    $this->setFlash('success', 'Usuário cadastrado com sucesso! Faça login.');
                    $this->redirect('auth/login');
                }

                $error = 'Não foi possível cadastrar. Tente novamente.';
            }
        }

        $this->render('auth/register', [
            'error'      => $error,
            'csrf_token' => $this->generateCsrfToken(),
            'email'      => $_POST['email'] ?? '',
        ]);
    }

    /**
     * Encerra a sessão e volta para o login.
     */
    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        $this->redirect('auth/login');
    }
}
