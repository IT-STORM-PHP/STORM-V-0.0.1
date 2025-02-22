<?php

use App\Routes\Route;
use App\Controllers\HomeControllers;

Route::get('/', [HomeControllers::class, 'index']);



use App\Controllers\LivresController;
Route::get('/livres', [LivresController::class, 'index']);
Route::get('/livres/create', [LivresController::class, 'create']);
Route::get('/livres/show/{id}', [LivresController::class, 'show']);
Route::post('/livres/store', [LivresController::class, 'store']);
Route::get('/livres/edit/{id}', [LivresController::class, 'edit']);
Route::post('/livres/update/{id}', [LivresController::class, 'update']);
Route::post('/livres/delete/{id}', [LivresController::class, 'destroy']);


