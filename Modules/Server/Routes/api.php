<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Server\Http\Controllers\Panel\PackageController;
use Modules\Server\Http\Controllers\Panel\PackageDurationController;
use Modules\Server\Http\Controllers\Panel\ServerController;
use Modules\Server\Http\Controllers\Panel\ServiceController;

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

Route::prefix('/panel')->group(function () {
    Route::apiResource("servers", ServerController::class);
    Route::apiResource("package/durations", PackageDurationController::class);
    Route::apiResource("packages", PackageController::class);
    Route::apiResource("services", ServiceController::class);
});
