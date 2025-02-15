<?php
    use App\Routes\Route;
    use App\Controllers\HomeControllers;

    Route::get('/', [HomeControllers::class, 'index']);

use App\Controllers\CategoriesController;
Route::get('/categories', [CategoriesController::class, 'index']);
Route::get('/categories/create', [CategoriesController::class, 'create']);
Route::post('/categories/store', [CategoriesController::class, 'store']);
Route::get('/categories/edit/{id}', [CategoriesController::class, 'edit']);
Route::post('/categories/update/{id}', [CategoriesController::class, 'update']);
Route::post('/categories/delete/{id}', [CategoriesController::class, 'destroy']);
Route::get('/categories/show/{id}', [CategoriesController::class, 'show']);
