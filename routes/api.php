<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StatusController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\JourneyController;
use App\Http\Controllers\DropoffController;
use App\Http\Controllers\LocateController;

Route::get('status', [StatusController::class, 'index']);
Route::put('cars', [CarController::class, 'update']);
Route::post('journey', [JourneyController::class, 'store']);
Route::post('dropoff', [DropoffController::class, 'store']);
Route::post('locate', [LocateController::class, 'locate']);
