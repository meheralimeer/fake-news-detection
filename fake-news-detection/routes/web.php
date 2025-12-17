<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PredictionController;

Route::get('/', [PredictionController::class, 'index'])->name('home');
Route::post('/predict', [PredictionController::class, 'predict'])->name('predict');
Route::get('/history/{id?}', [PredictionController::class, 'history'])->name('history');
