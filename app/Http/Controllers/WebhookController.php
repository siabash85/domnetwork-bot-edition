<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\User\Entities\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    public function callback(Request $request)
    {

        // $button1 = ['text' => 'Button 1'];
        // $button2 = ['text' => 'Button 2'];

        // $keyboard = [[$button1, $button2]];

        // $replyMarkup = json_encode([
        //     'keyboard' => $keyboard,
        //     'resize_keyboard' => true,
        //     'one_time_keyboard' => true,
        // ]);
        // $messageText = 'Hello! Please choose one of the buttons:';


        // $message = $request['message']['text'];
        // $telegramUser = $request['message']['from'];
        // $userId = $telegramUser['id'];
        Http::asForm()->post("https://api.telegram.org/bot6627556212:AAHLM2Z_iUJTVmKyyXmsXSyzpRiFpc9umSs/sendMessage", [
            "chat_id"  => "1669306764",
            "text"  => "test message eeee ss rr"
        ]);

        // if ($message === '/start') {
        //     Http::asForm()->post("https://api.telegram.org/bot6627556212:AAHLM2Z_iUJTVmKyyXmsXSyzpRiFpc9umSs/sendMessage", [
        //         "chat_id"  => "1669306764",
        //         'text' => json_encode($request['message'], true),
        //         // 'reply_markup' => $replyMarkup,
        //     ]);
        //     $user = User::where('uid', $userId)->first();
        //     if (!$user) {
        //         User::query()->create([
        //             'username' => $telegramUser['username'],
        //             'first_name' => $telegramUser['first_name'],
        //             'uid' => $userId,
        //             'password' => Hash::make(Str::random(8)),
        //         ]);
        //         Http::asForm()->post("https://api.telegram.org/bot6627556212:AAHLM2Z_iUJTVmKyyXmsXSyzpRiFpc9umSs/sendMessage", [
        //             "chat_id"  => "1669306764",
        //             'text' => json_encode($request['message'], true),
        //             // 'reply_markup' => $replyMarkup,
        //         ]);
        //     }
        // }
    }
}
