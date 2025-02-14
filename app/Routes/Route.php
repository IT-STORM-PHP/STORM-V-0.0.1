<?php
namespace App\Routes;

class Route {
    private static array $routes = [];
    private static array $middlewares = [];

    /**
     * Enregistre une route avec une méthode HTTP spécifique.
     */
    public static function add(string $method, string $uri, array|string|callable $action) {
        self::$routes[strtoupper($method)][$uri] = $action;
    }

    /**
     * Alias pour enregistrer des routes GET, POST, PUT, DELETE.
     */
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

    /**
     * Ajoute un middleware avant une route.
     */
    public static function beforeMiddleware(string $uri, callable $middleware) {
        self::$middlewares[$uri] = $middleware;
    }

    /**
     * Gère l'exécution des routes.
     */
    public static function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = strtok($_SERVER['REQUEST_URI'], '?'); // Supprime les paramètres GET
        $foundRoute = false;

        foreach (self::$routes as $routeMethod => $routes) {
            foreach ($routes as $route => $action) {
                $pattern = preg_replace('#\{(\w+)\}#', '([^/]+)', $route);

                if (preg_match("#^$pattern$#", $uri, $matches)) {
                    array_shift($matches); // Supprime l'URL complète du tableau

                    // Si la méthode existe, exécuter la route
                    if ($routeMethod === $method) {
                        return self::handleRoute($route, $action, $matches);
                    }

                    // Route trouvée mais avec mauvaise méthode -> 405
                    $foundRoute = true;
                }
            }
        }

        if ($foundRoute) {
            http_response_code(405);
            echo "405 - Méthode non autorisée";
            exit();
        }

        // Route non trouvée -> 404
        http_response_code(404);
        echo "404 - Page introuvable";
        exit();
    }

    /**
     * Exécute une route en tenant compte des middlewares.
     */
    private static function handleRoute($route, $action, $params) {
        // Exécute le middleware avant la route si défini
        if (isset(self::$middlewares[$route])) {
            call_user_func(self::$middlewares[$route]);
        }

        // Exécute l'action correspondante
        return self::execute($action, $params);
    }

    /**
     * Exécute l'action d'une route.
     */
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
