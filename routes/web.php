<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailConfirmWebController;

Route::get('/confirmar-email/{token}', [EmailConfirmWebController::class, 'confirmar']);