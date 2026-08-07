<?php
// app/Core/Router.php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        // Strip query string
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Azure serves the app through /index.php
        if ($uri === '/index.php') {
            $uri = '/';
        }

        // Remove trailing slash (except root)
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {

            $regex = preg_replace(
                '/\/:([^\/]+)/',
                '/(?P<$1>[^\/]+)',
                $pattern
            );

            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $uri, $matches)) {

                $params = array_filter(
                    $matches,
                    'is_string',
                    ARRAY_FILTER_USE_KEY
                );

                [$class, $action] = $handler;

                $controller = new $class();
                $controller->$action($params);

                return;
            }
        }

        http_response_code(404);
        require APP_ROOT . '/public/404.php';
    }
}
