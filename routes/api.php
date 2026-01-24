<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ErrorController;
use App\Http\Controllers\Api\v1\TicketController;
use App\Http\Controllers\Api\v1\FeatureController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    //ticket routes
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::put('/tickets/{id}', [TicketController::class, 'update']);
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy']);
    
    //error report routes
    Route::get('/error-reports', [ErrorController::class, 'index']);
    Route::get('/error-reports/{id}', [ErrorController::class, 'show']);
    Route::post('/error-reports', [ErrorController::class, 'store']);
    Route::put('/error-reports/{id}', [ErrorController::class, 'update']);
    Route::delete('/error-reports/{id}', [ErrorController::class, 'destroy']);
    
    //feature request routes
    Route::get('/feature-requests', [FeatureController::class, 'index']);
    Route::get('/feature-requests/{id}', [FeatureController::class, 'show']);
    Route::post('/feature-requests', [FeatureController::class, 'store']);
    Route::put('/feature-requests/{id}', [FeatureController::class, 'update']);
    Route::delete('/feature-requests/{id}', [FeatureController::class, 'destroy']);
    
});
