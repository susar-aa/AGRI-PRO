<?php
namespace Core;

class Router {
    private array $routes = [];

    public function get(string $path, array|callable $handler): void {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, array|callable $handler): void {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, array|callable $handler): void {
        $path = '/' . trim($path, '/');
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Normalize base path
        $config = require __DIR__ . '/../config/app.php';
        $baseUrl = parse_url($config['base_url'], PHP_URL_PATH) ?? '';
        
        if ($baseUrl && strpos($requestUri, $baseUrl) === 0) {
            $requestUri = substr($requestUri, strlen($baseUrl));
        }

        $requestUri = '/' . trim($requestUri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod && $route['path'] === $requestUri) {
                $handler = $route['handler'];

                if (is_callable($handler)) {
                    call_user_func($handler);
                    return;
                }

                if (is_array($handler)) {
                    [$controllerClass, $method] = $handler;
                    if (class_exists($controllerClass)) {
                        $controller = new $controllerClass();
                        if (method_exists($controller, $method)) {
                            $controller->$method();
                            return;
                        }
                    }
                }
            }
        }

        // 404 Fallback
        http_response_code(404);
        echo "<h1>404 Not Found</h1><p>The requested page [{$requestUri}] was not found on Agri Co-Op ERP.</p>";
    }
}
