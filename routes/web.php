<?php
    use App\Routes\Route;
    use App\Controllers\HomeControllers;
    
    Route::get('/', [HomeControllers::class, 'index']);

use App\Controllers\Login\LoginController;
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register']);
