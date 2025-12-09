<?php

use App\Http\Controllers\PredictionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/predict', [PredictionController::class, 'predict']);
