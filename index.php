<?php
/**
 * Front Controller — Ponto de entrada único do sistema
 *
 * Toda requisição passa por aqui.
 * Responsabilidades:
 *  1. Iniciar a sessão
 *  2. Carregar as configurações
 *  3. Registrar o autoloader de classes
 *  4. Registrar todas as rotas
 *  5. Despachar a requisição para o controller correto
 */

// 1. Inicia a sessão PHP
session_start();

// 2. Carrega as configurações (constantes de banco e BASE_URL)
require_once __DIR__ . '/config/config.php';

// 3. Autoloader PSR-4 manual — carrega classes automaticamente por namespace
//    Converte 'App\Core\Database' em 'app/Core/Database.php'
spl_autoload_register(function (string $class): void {
    // Remove o prefixo 'App\' e converte '\' em separador de diretório
    $path = __DIR__ . '/app/' . str_replace('\\', '/', substr($class, 4)) . '.php';

    if (file_exists($path)) {
        require_once $path;
    }
});

// 4. Instancia o roteador e registra todas as rotas do sistema
use App\Core\Router;

$router = new Router();

// --- Rotas de autenticação ---
$router->add('auth/login',    'AuthController', 'login');
$router->add('auth/logout',   'AuthController', 'logout');
$router->add('auth/register', 'AuthController', 'register');

// --- Rota principal (dashboard) ---
$router->add('dashboard', 'DashboardController', 'index');

// --- Rotas de serviços ---
$router->add('service/create', 'ServiceController', 'create');
$router->add('service/store',  'ServiceController', 'store');
$router->add('service/edit',   'ServiceController', 'edit');
$router->add('service/update', 'ServiceController', 'update');
$router->add('service/delete', 'ServiceController', 'delete');
$router->add('service/finish', 'ServiceController', 'finish');

// 5. Despacha a requisição
$router->dispatch();
