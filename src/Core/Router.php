<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->routes[] = [
            'method' => 'GET',
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function post(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->routes[] = [
            'method' => 'POST',
            'path' => $path,
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch(string $uri, string $method): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }

            $params = $this->matchRoute($route['path'], $uri);
            if ($params !== false) {
                foreach ($route['middlewares'] as $middleware) {
                    $mw = new $middleware();
                    $mw->handle();
                }

                $handler = $route['handler'];

                if (is_callable($handler)) {
                    call_user_func($handler, ...array_values($params));
                    return;
                }

                [$controllerClass, $action] = $handler;
                $controller = new $controllerClass();
                $controller->$action(...array_values($params));
                return;
            }
        }

        http_response_code(404);
        echo '<!DOCTYPE html><html><head><title>404 Not Found</title><script src="https://cdn.tailwindcss.com"></script></head>';
        echo '<body class="bg-slate-900 text-slate-100 flex items-center justify-center h-screen">';
        echo '<div class="text-center"><h1 class="text-6xl font-bold text-blue-500">404</h1><p class="text-2xl mt-4">Page Not Found</p>';
        echo '<a href="' . ($_ENV['APP_URL'] ?? '') . '/tickets/list" class="mt-6 inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Go Home</a></div></body></html>';
    }

    private function matchRoute(string $routePath, string $uri): array|false
    {
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '([^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $uri, $matches)) {
            return false;
        }

        preg_match_all('/\{([a-zA-Z_]+)\}/', $routePath, $paramNames);
        array_shift($matches);

        return array_combine($paramNames[1], $matches) ?: [];
    }
}
