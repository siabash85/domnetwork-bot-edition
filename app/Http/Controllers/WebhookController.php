<?php

namespace App\Http\Controllers;

use App\Telegram\Keyboard\Keyboards;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\User\Entities\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Modules\Server\Entities\Package;
use Modules\Server\Entities\PackageDuration;
use Modules\Server\Entities\Server;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebhookController extends Controller
{
    public function callback(Request $request)
    {

        $update = Telegram::commandsHandler(true);
        $sender = $update->getMessage()->from;
        $user = User::query()->where('uid', $sender->id)->first();

        // Telegram::sendMessage([
        //     'text' => $update->getMessage()->text . "aa",
        //     "chat_id" => "1669306764"
        // ]);
        if ($update->getMessage()->text !== "/start") {
            switch ($update->getMessage()->text) {
                case Keyboards::PURCHASE_SERVICE:
                    $servers = Server::query()->where('is_active', true)->where('stock', '>=', 1)->get();

                    if (count($servers) == 0) {
                        $user->update([
                            'section' => Keyboards::PURCHASE_SERVICE,
                            'step' => 2
                        ]);
                        $durations = PackageDuration::query()->get();
                        $durationButtons = collect($durations)->map(function ($duration) {
                            return ['text' => $duration->name];
                        })->chunk(3)->toArray();
                        $replyMarkup = [
                            'keyboard' => $durationButtons,
                            'resize_keyboard' => true,
                            'one_time_keyboard' => false,
                        ];
                        $encodedMarkup = json_encode($replyMarkup);

                        Telegram::sendMessage([
                            'text' => "⏳ مدت زمان سرویس را انتخاب کنید:",
                            "chat_id" => $sender->id,
                            'reply_markup' => $encodedMarkup,
                        ]);
                    } else {
                        $serverButtons = collect($servers)->map(function ($server) {
                            return ['text' => $server->name];
                        })->chunk(2)->toArray();
                        $replyMarkup = [
                            'keyboard' => $serverButtons,
                            'resize_keyboard' => true,
                            'one_time_keyboard' => true,
                        ];
                        $encodedMarkup = json_encode($replyMarkup);
                        $user->update([
                            'section' => Keyboards::PURCHASE_SERVICE,
                            'step' => 1
                        ]);
                        Telegram::sendMessage([
                            'text' => "🌍 لوکیشن که میخواهید از آن سرویس تهیه کنید را انتخاب کنید : ",
                            "chat_id" => $sender->id,
                            'reply_markup' => $encodedMarkup,
                        ]);
                    }


                    break;
                default:
                    $servers = Server::query()->pluck('name')->toArray();
                    $durations = PackageDuration::query()->pluck('name')->toArray();
                    if (in_array($update->getMessage()->text, $servers)) {
                        if ($user->step == "1" && $user->section == Keyboards::PURCHASE_SERVICE) {
                            $durations = PackageDuration::query()->get();
                            $durationButtons = collect($durations)->map(function ($duration) {
                                return ['text' => $duration->name];
                            })->chunk(3)->toArray();
                            $replyMarkup = [
                                'keyboard' => $durationButtons,
                                'resize_keyboard' => true,
                                'one_time_keyboard' => false,
                            ];
                            $encodedMarkup = json_encode($replyMarkup);
                            $user->update([
                                'section' => Keyboards::PURCHASE_SERVICE,
                                'step' => 2
                            ]);
                            Telegram::sendMessage([
                                'text' => "⏳ مدت زمان سرویس را انتخاب کنید:",
                                "chat_id" => $sender->id,
                                'reply_markup' => $encodedMarkup,
                            ]);
                        }
                    } else if (in_array($update->getMessage()->text, $durations)) {
                        if ($user->step == "2" && $user->section == Keyboards::PURCHASE_SERVICE) {
                            $packages = Package::query()->get();
                            $packageButtons = collect($packages)->map(function ($package) {
                                return ['text' => $package->name];
                            })->chunk(3)->toArray();
                            $replyMarkup = [
                                'keyboard' => $packageButtons,
                                'resize_keyboard' => true,
                                'one_time_keyboard' => false,
                            ];
                            $encodedMarkup = json_encode($replyMarkup);
                            $user->update([
                                'section' => Keyboards::PURCHASE_SERVICE,
                                'step' => 3
                            ]);
                            Telegram::sendMessage([
                                'text' => "🔰لطفا یکی از پلن های زیر را انتخاب کنید :",
                                "chat_id" => $sender->id,
                                'reply_markup' => $encodedMarkup,
                            ]);
                        }
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
