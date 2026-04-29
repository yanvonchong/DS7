<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

// 👇 AGREGA ESTA LÍNEA
Route::resource('products', ProductController::class);