<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::post('/menu/redirect-login', [MenuController::class, 'redirectToLogin'])->name('menu.redirect-login');

// Optionally, make / show the menu by default:
Route::get('/', [MenuController::class, 'index']);