<?php
namespace App\Routes;

class Route {
    private static array $routes = [];
    private static array $middlewares = [];
    private static array $beforeMiddlewares = [];

    public static function add(string $method, string $uri, array|string|callable $action) {
        self::$routes[strtoupper($method)][$uri] = $action;
    }

    public static function get(string $uri, array|string|callable $action) {
        self::add('GET', $uri, $action);
    }

    public static function post(string $uri, array|string|callable $action) {
        self::add('POST', $uri, $action);
    }

    public static function put(string $uri, array|string|callable $action) {
        self::add('PUT', $uri, $action);
    }

    public static function delete(string $uri, array|string|callable $action) {
        self::add('DELETE', $uri, $action);
    }

    public static function middleware(string $uri, callable|array $middleware) {
        self::$middlewares[$uri][] = $middleware;
    }

    public static function beforeMiddleware(string $prefix, callable $callback) {
        self::$beforeMiddlewares[$prefix][] = $callback;
    }

    public static function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $foundRoute = false;

        foreach (self::$beforeMiddlewares as $prefix => $callbacks) {
            if (strpos($uri, $prefix) === 0) {
                foreach ($callbacks as $callback) {
                    call_user_func($callback);
                }
            }
        }

        foreach (self::$routes as $routeMethod => $routes) {
            foreach ($routes as $route => $action) {
                $pattern = preg_replace('#\{(\w+)\}#', '([^/]+)', $route);
                if (preg_match("#^$pattern$#", $uri, $matches)) {
                    array_shift($matches);
                    if ($routeMethod === $method) {
                        return self::handleRoute($route, $action, $matches);
                    }
                    $foundRoute = true;
                }
            }
        }

        if ($foundRoute) {
            http_response_code(405);
            echo "405 - Méthode non autorisée";
            exit();
        }

        http_response_code(404);
        echo "404 - Page introuvable";
        exit();
    }

    private static function handleRoute($route, $action, $params) {
        if (isset(self::$middlewares[$route])) {
            foreach (self::$middlewares[$route] as $middleware) {
                if (is_array($middleware) && count($middleware) === 2) {
                    [$class, $method] = $middleware;
                    if (class_exists($class) && method_exists($class, $method)) {
                        call_user_func([new $class, $method]);
                    }
                } else {
                    call_user_func($middleware);
                }
            }
        }

        return self::execute($action, $params);
    }

    private static function execute($action, $params) {
        if (is_callable($action)) {
            echo call_user_func_array($action, $params);
        } elseif (is_array($action) && count($action) === 2) {
            [$controller, $method] = $action;
            if (!class_exists($controller)) {
                http_response_code(500);
                echo "500 - Erreur interne: Contrôleur '$controller' introuvable.";
                exit();
            }

            $controllerInstance = new $controller();
            if (!method_exists($controllerInstance, $method)) {
                http_response_code(500);
                echo "500 - Erreur interne: Méthode '$method' non trouvée dans '$controller'.";
                exit();
            }

            echo call_user_func_array([$controllerInstance, $method], $params);
        } else {
            http_response_code(500);
            echo "500 - Erreur interne: Action non valide.";
            exit();
        }
    }
}
?>

