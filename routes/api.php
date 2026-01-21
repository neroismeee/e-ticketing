<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\ErrorController;
use App\Http\Controllers\Api\FeatureController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//ticket routes
Route::get('/tickets', [TicketController::class, 'index']);
Route::get('/tickets/{id}', [TicketController::class, 'show']);

//error report routes
Route::get('/error-reports', [ErrorController::class, 'index']);

//feature request routes
Route::get('/feature-requests', [FeatureController::class, 'index']);
