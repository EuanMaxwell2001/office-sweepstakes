<?php

use App\Http\Controllers\SweepstakeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SweepstakeController::class, 'index']);
