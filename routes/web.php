<?php
    use App\Routes\Route;
    use App\Controllers\HomeControllers;

    Route::get('/', [HomeControllers::class, 'index']);

use App\Controllers\TestController;
Route::get('/test', [TestController::class, 'index']);
Route::get('/test/create', [TestController::class, 'create']);
Route::get('/test/show/{id}', [TestController::class, 'show']);
Route::post('/test/store', [TestController::class, 'store']);
Route::get('/test/edit/{id}', [TestController::class, 'edit']);
Route::post('/test/update/{id}', [TestController::class, 'update']);
Route::post('/test/delete/{id}', [TestController::class, 'destroy']);
