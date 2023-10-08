<?php

namespace App\Http\Controllers;

use App\Telegram\Keyboard\Keyboards;
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
        $sender = $update->getMessage()->from;

        // Telegram::sendMessage([
        //     'text' => $update->getMessage()->text . "aa",
        //     "chat_id" => "1669306764"
        // ]);
        if ($update->getMessage()->text !== "/start") {
            switch ($update->getMessage()->text) {
                case Keyboards::PURCHASE_SERVICE:
                    Telegram::sendMessage([
                        'text' => "🌍 لوکیشن که میخواهید از آن سرویس تهیه کنید را انتخاب کنید : ",
                        "chat_id" => $sender->id,
                    ]);
                    break;
                default:
                    $options = ['Option 1', 'Option 2', 'Option 3']; // read form database
                    if (in_array($update->getMessage()->text, $options)) {
                        // search state from database
                        // $valid_step = UserStates::query()->where('section', Keyboards::GUIDE)->where('state', 1)->first();
                        // if (is_null($valid_step)) {

                        // }
                    }
                    break;
            }
        }


        // $update = Telegram::commandsHandler(false, ['timeout' => 4]);

        // $query = $update->getCallbackQuery();
        // $callback_id = $query->getId();
        // Telegram::sendMessage([
        //     'text' => $callback_id,
        //     'chat_id' => "1669306764"
        // ]);

        return 'ok';
    }
}
