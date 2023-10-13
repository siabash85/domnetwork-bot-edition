<?php

namespace App\Http\Controllers;

use App\Telegram\Keyboard\Keyboards;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\User\Entities\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\PreOrder;
use Modules\Server\Entities\Package;
use Modules\Server\Entities\PackageDuration;
use Modules\Server\Entities\Server;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebhookController extends Controller
{
    public function callback(Request $request)
    {



        $update = Telegram::commandsHandler(true);

        http_response_code(200);
        $sender = $update->getMessage()->from;
        $user = User::query()->where('uid', $sender->id)->first();


        // Telegram::sendMessage([
        //     'text' => "⏳ aa",
        //     "chat_id" => $sender->id,

        // ]);
        // return true;



        if ($update->getMessage()->text !== "/start") {


            if ($update->getMessage()->text == Keyboards::PURCHASE_SERVICE) {
                $servers = Server::query()->where('is_active', true)->get();
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
                    $keyboards = [];
                    $keyboards_keyboards = $servers->chunk(2);
                    foreach ($keyboards_keyboards as $chunk) {
                        $row = [];
                        foreach ($chunk as $server) {
                            $row[] = ['text' => $server->name];
                        }
                        $keyboards[] = $row;
                    }

                    $replyMarkup = [
                        'keyboard' => $keyboards,
                        'resize_keyboard' => true,
                        'one_time_keyboard' => false,
                    ];
                    $encodedMarkup = json_encode($replyMarkup);
                    Telegram::sendMessage([
                        'text' => "🌍 لوکیشن که میخواهید از آن سرویس تهیه کنید را انتخاب کنید : ",
                        'chat_id' => $sender->id,
                        'reply_markup' => $encodedMarkup,
                    ]);
                    $user->update([
                        'section' => Keyboards::PURCHASE_SERVICE,
                        'step' => 1
                    ]);
                }
            }

            $servers = Server::query()->pluck('name')->toArray();
            $durations = PackageDuration::query()->pluck('name')->toArray();
            $packages = Package::query()->pluck('name')->toArray();



            if (in_array($update->getMessage()->text, $servers)) {
                if ($user->step == "1" && $user->section == Keyboards::PURCHASE_SERVICE) {
                    $selected_server = Server::query()->where('name', $update->getMessage()->text)->first();
                    $pre_order = PreOrder::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'user_id' => $user->id,
                            'server_id' => $selected_server->id,
                        ]
                    );
                    $durations = PackageDuration::query()->get();
                    $keyboards = [];
                    $keyboards_keyboards = $durations->chunk(2);
                    foreach ($keyboards_keyboards as $chunk) {
                        $row = [];
                        foreach ($chunk as $duration) {
                            $row[] = ['text' => $duration->name];
                        }
                        $keyboards[] = $row;
                    }
                    $replyMarkup = [
                        'keyboard' => $keyboards,
                        'resize_keyboard' => true,
                        'one_time_keyboard' => false,
                    ];
                    $encodedMarkup = json_encode($replyMarkup);

                    Telegram::sendMessage([
                        'text' => "⏳ مدت زمان سرویس را انتخاب کنید:",
                        "chat_id" => $sender->id,
                        'reply_markup' => $encodedMarkup,
                    ]);
                    $user->update([
                        'section' => Keyboards::PURCHASE_SERVICE,
                        'step' => 2
                    ]);
                }
            } else if (in_array($update->getMessage()->text, $durations)) {
                if ($user->step == "2" && $user->section == Keyboards::PURCHASE_SERVICE) {
                    $packages = Package::query()->get();
                    $keyboards = [];
                    $keyboards_keyboards = $packages->chunk(2);
                    foreach ($keyboards_keyboards as $chunk) {
                        $row = [];
                        foreach ($chunk as $package) {
                            $row[] = ['text' => $package->name];
                        }
                        $keyboards[] = $row;
                    }
                    $replyMarkup = [
                        'keyboard' => $keyboards,
                        'resize_keyboard' => true,
                        'one_time_keyboard' => false,
                    ];
                    $encodedMarkup = json_encode($replyMarkup);
                    Telegram::sendMessage([
                        'text' => "🔰لطفا یکی از پلن های زیر را انتخاب کنید :",
                        "chat_id" => $sender->id,
                        'reply_markup' => $encodedMarkup,
                    ]);
                    $pre_order = PreOrder::query()->where('user_id', $user->id)->first();
                    $selected_duration = PackageDuration::query()->where('name', $update->getMessage()->text)->first();
                    $pre_order->update([
                        'package_duration_id' => $selected_duration->id
                    ]);
                    $user->update([
                        'section' => Keyboards::PURCHASE_SERVICE,
                        'step' => 3
                    ]);
                }
            } else if (in_array($update->getMessage()->text, $packages)) {
                if ($user->step == "3" && $user->section == Keyboards::PURCHASE_SERVICE) {

                    $pre_order = PreOrder::query()->where('user_id', $user->id)->first();
                    $selected_package = Package::query()->where('name', $update->getMessage()->text)->first();
                    $pre_order->update([
                        'package_id' => $selected_package->id
                    ]);
                    $order =   Order::query()->create([
                        "user_id" => $user->id,
                        "server_id" => $pre_order->server_id,
                        "package_duration_id" =>  $pre_order->package_duration_id,
                        "package_id" =>  $selected_package->id,
                        "status" =>  "pending",
                        "payable_price" =>  $selected_package->price,
                        "price" =>  $selected_package->price,
                    ]);


                    $location = $order->server->name;
                    $volume = $order->package->name;
                    $date = $order->package_duration->name;
                    $trackingCode = $order->reference_code;
                    $amount = "{$order->package->price} تومان";
                    $message = "ℹ️ فاکتور شما با جزئیات زیر با موفقیت ساخته شد.\n\n" .
                        "⬅️ لوکیشن : $location\n" .
                        "⬅️ حجم : $volume\n" .
                        "⬅️ تاریخ : $date\n" .
                        "⬅️ کد پیگیری : $trackingCode\n\n" .
                        "💸 مبلغ سرویس شما : $amount\n\n" .
                        " 👇🏻 در صورت تایید اطلاعات بالا میتوانید از طریق دکمه های زیر پرداخت خود را انجام بدید.";

                    $res = Http::post("https://panel.aqayepardakht.ir/api/v2/create", [
                        "pin" => "sandbox",
                        "amount" => $order->price,
                        "callback" => "https://pashmak-titab.store/api/client/payment/callback",
                    ]);
                    $dd = json_decode($res->body());
                    $transid = $dd->transid;
                    $inlineKeyboard = [
                        [
                            [
                                'text' => 'درگاه پرداخت',
                                'url' => route('payment.generate', ['order' => $order->id, 'id' => $transid])
                            ],
                        ],

                    ];
                    $encodedKeyboard = json_encode(['inline_keyboard' => $inlineKeyboard]);
                    Telegram::sendMessage([
                        'text' => $message,
                        "chat_id" => $sender->id,
                        'reply_markup' => $encodedKeyboard,
                    ]);

                    $user->update([
                        'section' => Keyboards::PURCHASE_SERVICE,
                        'step' => 4
                    ]);
                }
            }
        }
        return 'ok';
    }
}
