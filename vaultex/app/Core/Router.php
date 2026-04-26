<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private string $basePath = '';

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function get(string $path, array $handler): self
    {
        $this->addRoute('GET', $path, $handler);
        return $this;
    }

    public function post(string $path, array $handler): self
    {
        $this->addRoute('POST', $path, $handler);
        return $this;
    }

    private function addRoute(string $method, string $path, array $handler): void
    {
        $path = $this->normalizePath($path);
        $this->routes[$method][$path] = $handler;
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? $path : rtrim($path, '/');
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        $uri = $this->normalizePath($uri);

        // Check for exact match
        if (isset($this->routes[$method][$uri])) {
            [$controller, $action] = $this->routes[$method][$uri];
            $this->callAction($controller, $action);
            return;
        }

        // Check for parameterized routes
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$controller, $action] = $handler;
                $this->callAction($controller, $action, $params);
                return;
            }
        }

        http_response_code(404);
        echo "404 - Page Not Found";
    }

    private function callAction(string $controller, string $action, array $params = []): void
    {
        if (!class_exists($controller)) {
            throw new \Exception("Controller {$controller} not found");
        }

        $instance = new $controller();
        if (!method_exists($instance, $action)) {
            throw new \Exception("Action {$action} not found in {$controller}");
        }

        call_user_func_array([$instance, $action], $params);
    }

    public function getUrl(string $name, array $params = []): string
    {
        // Simple URL generation - can be extended
        return $this->basePath . '/' . ltrim($name, '/');
    }
}
