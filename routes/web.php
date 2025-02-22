<?php

use App\Routes\Route;
use App\Controllers\HomeControllers;

Route::get('/', [HomeControllers::class, 'index']);

