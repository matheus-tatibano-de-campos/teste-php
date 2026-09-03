<?php
/**
 * Classe Router — Roteador simples GET/POST
 *
 * Mapeia rotas (strings) para Controller + método.
 * A rota vem do parâmetro GET "route" na URL.
 *
 * Exemplo de URL:  http://localhost/teste-php/teste-php/?route=dashboard
 * O Router identifica 'dashboard' e chama DashboardController::index()
 */

namespace App\Core;

class Router
{
    // Tabela de rotas: 'rota' => ['Controller', 'metodo']
    private array $routes = [];

    /**
     * Registra uma rota.
     *
     * @param string $route      Nome da rota (ex: 'auth/login')
     * @param string $controller Nome da classe controller (ex: 'AuthController')
     * @param string $action     Nome do método a chamar (ex: 'login')
     */
    public function add(string $route, string $controller, string $action): void
    {
        $this->routes[$route] = [
            'controller' => $controller,
            'action'     => $action,
        ];
    }

    /**
     * Despacha a requisição para o controller/método correto.
     * Lê o parâmetro "route" da URL; usa 'auth/login' como padrão.
     */
    public function dispatch(): void
    {
        // Obtém a rota da URL, sanitizando a entrada
        $route = trim($_GET['route'] ?? 'auth/login');
        $route = filter_var($route, FILTER_SANITIZE_URL);

        if (!isset($this->routes[$route])) {
            http_response_code(404);
            die('<h2>Página não encontrada (404)</h2>');
        }

        $controllerName = 'App\\Controllers\\' . $this->routes[$route]['controller'];
        $action         = $this->routes[$route]['action'];

        // Verifica se a classe e o método existem antes de chamar
        if (!class_exists($controllerName)) {
            die("Controller não encontrado: {$controllerName}");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $action)) {
            die("Método não encontrado: {$action} em {$controllerName}");
        }

        // Chama o método do controller
        $controller->$action();
    }
}
