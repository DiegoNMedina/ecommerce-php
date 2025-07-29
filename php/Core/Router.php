<?php

namespace Core;

class Router
{
    private array $routes = [];
    private string $basePath;
    private ?string $matchedRoute = null;

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function add(string $method, string $pattern, array $handler): void
    {
        $pattern = $this->basePath . '/' . trim($pattern, '/');
        $pattern = preg_replace('/\/+/', '/', $pattern);

        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
            'params' => []
        ];
    }

    public function get(string $pattern, array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, array $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function match(): ?array
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestUri = urldecode($requestUri);

        foreach ($this->routes as &$route) {
            $pattern = preg_replace('/\{([^\}]+)\}/', '(?P<\1>[^/]+)', $route['pattern']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $requestUri, $matches) && $route['method'] === $requestMethod) {
                $this->matchedRoute = $route['pattern'];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $route['params'][$key] = $value;
                    }
                }
                return $route;
            }
        }

        return null;
    }

    public function dispatch(): void
    {
        $route = $this->match();

        if (!$route) {
            http_response_code(404);
            require dirname(__DIR__, 2) . '/public_html/views/errors/404.php';
            return;
        }

        // Establecer código de estado 200 para rutas encontradas
        http_response_code(200);

        [$controller, $action] = $route['handler'];
        $params = $route['params'];

        if (!class_exists($controller)) {
            throw new \Exception("Controller not found: {$controller}");
        }

        $controller = new $controller();

        if (!method_exists($controller, $action)) {
            throw new \Exception("Action not found: {$action}");
        }

        call_user_func_array([$controller, $action], $params);
    }

    public function getMatchedRoute(): ?string
    {
        return $this->matchedRoute;
    }
}
