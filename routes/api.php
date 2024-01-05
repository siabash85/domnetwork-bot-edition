<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Modules\User\Transformers\Panel\UserResource;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return new UserResource($request->user());
});
Route::prefix('/auth')->group(function () {
    Route::post("login", [AuthenticatedSessionController::class, 'store']);
    Route::post("logout", [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:api');
});

Route::post('/webhook/callback', [WebhookController::class, 'callback']);
