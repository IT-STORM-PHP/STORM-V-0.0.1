<?php
namespace App\Views;

class View {
    /**
     * Render une vue dynamique.
     */
    public static function render($path, $dt = []) {
        extract($dt);
        include __DIR__ . "/$path.php";
        include __DIR__ . "/base.php"; 
    }

    /**
     * Render une vue avec un layout
     */
    public static function renderWithLayout($layout, $template, $data = []) {
        $content = self::getRenderedView($template, $data);
        extract($data);
        include __DIR__ . "/layouts/$layout.php"; 
    }

    /**
     * Retourne le contenu d'une vue sous forme de string.
     */
    private static function getRenderedView($template, $data = []) {
        extract($data);
        ob_start();
        include __DIR__ . "/$template.php";
        return ob_get_clean();
    }

    /**
     * Redirige vers une URL.
     */
    public static function redirect($url) {
        header("Location: $url");
        exit();
    }

    /**
     * Retourne une réponse JSON.
     */
    public static function jsonResponse($data, int $status = 200) {
        header("Content-Type: application/json");
        http_response_code($status);
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Définit un message flash en session.
     */
    public static function setFlash($key, $message) {
        if (!session_id()) session_start();
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Récupère un message flash et le supprime.
     */
    public static function getFlash($key) {
        if (!session_id()) session_start();
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return null;
    }

    /**
     * Définit un cookie.
     */
    public static function setCookie($name, $value, $expire = 3600, $path = "/", $secure = false, $httponly = true) {
        setcookie($name, $value, time() + $expire, $path, "", $secure, $httponly);
    }

    /**
     * Récupère un cookie.
     */
    public static function getCookie($name) {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Génère une page d'erreur HTTP.
     */
    public static function renderErrorPage($code = 404, $message = "Page not found") {
        http_response_code($code);
        include __DIR__ . "/errors/{$code}.php";
        exit();
    }

    /**
     * Génère un fichier pour téléchargement.
     */
    public static function downloadFile($filePath, $fileName = null) {
        if (!file_exists($filePath)) {
            self::renderErrorPage(404, "Fichier non trouvé");
        }
        if (!$fileName) {
            $fileName = basename($filePath);
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit();
    }

    /**
     * Paginate un tableau de données.
     */
    public static function paginate(array $items, $page = 1, $perPage = 10) {
        $total = count($items);
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        return [
            'data' => array_slice($items, $offset, $perPage),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_pages' => $totalPages,
                'total_items' => $total
            ]
        ];
    }
}
?>
