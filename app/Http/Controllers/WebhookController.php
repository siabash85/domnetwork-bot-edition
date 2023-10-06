<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\User\Entities\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebhookController extends Controller
{
    public function callback(Request $request)
    {
        $update = Telegram::commandsHandler(true);
        return 'ok';
    }
}
