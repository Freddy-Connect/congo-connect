<?php

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->normalizePath($uri);
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 - Page not found';
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $action] = $handler;
            $controller = new $controllerClass();
            $controller->{$action}();
            return;
        }

        call_user_func($handler);
    }

    private function addRoute(string $method, string $path, callable|array $handler): void
    {
        $this->routes[$method][$this->normalizePath($path)] = $handler;
    }

    private function normalizePath(string $path): string
    {
        $cleanPath = parse_url($path, PHP_URL_PATH) ?? '/';
        $cleanPath = rtrim($cleanPath, '/');

        return $cleanPath === '' ? '/' : $cleanPath;
    }
}
