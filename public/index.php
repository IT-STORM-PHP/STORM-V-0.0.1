<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../routes/web.php';
    
    use App\Routes\Route;
    use App\Middleware\SessionMiddleware;
    SessionMiddleware::start();
    Route::dispatch();
?>
