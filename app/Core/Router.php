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
        // Remove query string
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';

        // --------------------------------------------------
        // Azure / Nginx compatibility
        //
        // Supports both:
        //   /
        //   /login
        //   /register
        //   /services
        //
        // and
        //
        //   /index.php
        //   /index.php/login
        //   /index.php/register
        //   /index.php/services
        // --------------------------------------------------
        if (str_starts_with($uri, '/index.php')) {
            $uri = substr($uri, strlen('/index.php'));

            if ($uri === '') {
                $uri = '/';
            }
        }

        // Remove trailing slash except for root
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