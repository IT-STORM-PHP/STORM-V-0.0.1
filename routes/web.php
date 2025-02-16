<?php
    use App\Routes\Route;
    use App\Controllers\HomeControllers;
    
    Route::get('/', [HomeControllers::class, 'index']);
use App\Controllers\DatetimesController;
Route::get('/datetimes', [DatetimesController::class, 'index']);
Route::get('/datetimes/create', [DatetimesController::class, 'create']);
Route::get('/datetimes/show/{id}', [DatetimesController::class, 'show']);
Route::post('/datetimes/store', [DatetimesController::class, 'store']);
Route::get('/datetimes/edit/{id}', [DatetimesController::class, 'edit']);
Route::post('/datetimes/update/{id}', [DatetimesController::class, 'update']);
Route::post('/datetimes/delete/{id}', [DatetimesController::class, 'destroy']);
