<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\Panel\UserMessageController;

Route::prefix('/panel')->middleware('auth:api')->group(function () {
    Route::apiResource("notification/user/messages", UserMessageController::class);
});
