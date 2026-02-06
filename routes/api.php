<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailValidationController;

Route::post('/gerar-validacao', [EmailValidationController::class, 'gerar']);
Route::get('/validar-email/{email}', [EmailValidationController::class, 'status']);