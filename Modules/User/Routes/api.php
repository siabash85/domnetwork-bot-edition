<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Panel\UserController;

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
    Route::apiResource("users", UserController::class);
    Route::get("user/select/search", [UserController::class, "select"]);
    Route::get("user/report/{id}", [UserController::class, "report"]);
});
