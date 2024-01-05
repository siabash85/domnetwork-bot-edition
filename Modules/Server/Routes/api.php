<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Server\Http\Controllers\Panel\PackageController;
use Modules\Server\Http\Controllers\Panel\PackageDurationController;
use Modules\Server\Http\Controllers\Panel\PricingController;
use Modules\Server\Http\Controllers\Panel\ServerController;
use Modules\Server\Http\Controllers\Panel\ServiceController;
use Modules\Server\Http\Controllers\Panel\SubscriptionController;

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

Route::prefix('/panel')->middleware('auth:api')->group(function () {
    Route::apiResource("servers", ServerController::class);
    Route::apiResource("package/durations", PackageDurationController::class);
    Route::apiResource("packages", PackageController::class);
    Route::apiResource("services", ServiceController::class);
    Route::apiResource("pricing", PricingController::class);
    Route::apiResource("subscriptions", SubscriptionController::class);
});
