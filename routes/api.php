<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/integrations/customers', [App\Http\Controllers\Api\IntegrationJobController::class, 'store']);
Route::get('/integrations/customers/{id}', [App\Http\Controllers\Api\IntegrationJobController::class, 'show']);
Route::put('/integrations/customers/{id}', [App\Http\Controllers\Api\IntegrationJobController::class, 'update']);
Route::delete('/integrations/customers/{id}', [App\Http\Controllers\Api\IntegrationJobController::class, 'destroy']);
