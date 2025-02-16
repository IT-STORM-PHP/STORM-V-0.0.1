<?php
    use App\Routes\Route;
    use App\Controllers\HomeControllers;
    
    Route::get('/', [HomeControllers::class, 'index']);

use App\Controllers\ArticlesController;
Route::get('/articles', [ArticlesController::class, 'index']);
Route::get('/articles/create', [ArticlesController::class, 'create']);
Route::get('/articles/show/{id}', [ArticlesController::class, 'show']);
Route::post('/articles/store', [ArticlesController::class, 'store']);
Route::get('/articles/edit/{id}', [ArticlesController::class, 'edit']);
Route::post('/articles/update/{id}', [ArticlesController::class, 'update']);
Route::post('/articles/delete/{id}', [ArticlesController::class, 'destroy']);

use App\Controllers\Login\LoginController;
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register']);