<?php

namespace App\Http\Controllers;

use DOMXPath;
use DOMDocument;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\User\Entities\User;
use Modules\Order\Entities\Order;
use Modules\Server\Entities\Server;
use App\Telegram\Keyboard\Keyboards;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Modules\Order\Entities\PreOrder;
use Modules\Server\Entities\Package;
use Modules\Server\Entities\Pricing;
use Modules\Server\Entities\Service;
use Modules\Payment\Entities\Payment;
use Telegram\Bot\FileUpload\InputFile;
use Illuminate\Support\Facades\Storage;
use Modules\Guide\Entities\GuidePlatform;
use Modules\Server\Entities\Subscription;
use App\Telegram\Keyboard\KeyboardHandler;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\Payment\Entities\PaymentMethod;
use Modules\Server\Entities\PackageDuration;
use Modules\Support\Entities\SupportMessage;
use Modules\User\Entities\WalletTransaction;
use Modules\Server\Entities\ExtensionService;
use Modules\User\Entities\VoucherTransaction;
use Modules\Guide\Entities\GuidePlatformClient;
use Modules\Server\Services\GenerateConfigService;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WebhookController extends Controller
{
    public function callback(Request $request)
    {
        $update = Telegram::commandsHandler(true);
        $sender = $update->getMessage()->from;
        $user = User::query()->where('uid', $sender->id)->first();
        if (isset($update->callback_query)) {
            $sender = $update->callback_query->message->chat;
            $user = User::query()->where('uid', $sender->id)->first();
            $callbackQueryId = $update->callback_query->id;
            $callbackData = $update->callback_query->data;
            $chatId = $update->callback_query->message->chat->id;
            $messageId = $update->callback_query->message->message_id;
            if ($callbackData == "online_purchase") {
                $wallet_trans = WalletTransaction::query()->where('user_id', $user->id)->first();
                $wallet_amount = $wallet_trans->amount;
                $payment_method = PaymentMethod::query()->where('is_default', true)->first();
                $res = Http::post("https://panel.aqayepardakht.ir/api/v2/create", [
                    "pin" => "sandbox",
                    "amount" => $wallet_amount,
                    "callback" => "https://pashmak-titab.store/api/client/wallet/payment/callback",
                ]);
                $dd = json_decode($res->body());
                $transid = $dd->transid;
                $payment = Payment::query()->create([
                    "paymentable_type" => User::class,
                    "paymentable_id" => $user->id,
                    "user_id" => $user->id,
                    "payment_method_id" => $payment_method->id,
                    "invoice_id" => $transid,
                    "amount" => $wallet_amount,
                    "status" => "pending",
                ]);
                $inlineKeyboard = [
                    [
                        [
                            'text' => 'پرداخت آنلاین',
                            'url' => "https://panel.aqayepardakht.ir/startpay/sandbox/{$transid}"
                        ],
                    ],
                ];
                $encodedKeyboard = json_encode(['inline_keyboard' => $inlineKeyboard]);
                $invoise_code = $payment->reference_code;
                $newMessageText = "📣 *فاکتور شما با موفقیت ساخته شد*\n\n" .
                    "💎 * شماره فاکتور:* `$invoise_code`\n" .
                    "💳 * مبلغ قابل پرداخت:* `$wallet_amount` " . "تومان\n";
                Telegram::editMessageText([
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                    'text' => $newMessageText,
                    'parse_mode' => 'MarkdownV2',
                    'reply_markup' => $encodedKeyboard,
                ]);
            } else if ($callbackData == "card") {
                $user->update([
                    'section' => Keyboards::CHARGE,
                    'step' => 2
                ]);
                $wallet_trans = WalletTransaction::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        // 'amount' => $amount,
                        "method" => "card",
                        'status' => "pending"
                    ]
                );

                Telegram::sendMessage([
                    'text' => "💸 لطفا مبلغی که میخواهید شارژ کنید را به لاتین حداقل 10,000 تومان ارسال کنید :",
                    "chat_id" => $sender->id,
                    // 'reply_markup' => $encodedMarkup,
                ]);
                return true;
            } else if ($callbackData == "voucher") {
                $user->update([
                    'section' => Keyboards::CHARGE,
                    'step' => 2
                ]);
                $wallet_trans = WalletTransaction::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        "method" => "voucher",
                        'status' => "pending"
                    ]
                );
                Telegram::sendMessage([
                    'text' => "🎁 کد ۱۰ رقمی ووچر الکترونیکی را ارسال کنید:",
                    "chat_id" => $sender->id,
                    // 'reply_markup' => $encodedMarkup,
                ]);
                return true;
            } else if ($callbackData == "purchase_wallet") {

                $order = Order::query()->where('user_id', $user->id)->where("status", "pending")->latest()->first();


                $pre_order = PreOrder::query()->where('user_id', $user->id)->first();
                if ($order->payable_price > $user->wallet) {
                    Telegram::sendMessage([
                        'text' => "❌ موجودی شما برای خرید این سرویس کافی نمیباشد ",
                        "chat_id" => $sender->id,
                        // 'reply_markup' => $encodedMarkup,
                    ]);
                } else {
                    try {
                        Telegram::sendMessage([
                            'text' => "🔄 در حال ساخت سرویس شما . . .",
                            "chat_id" => $sender->id,
                        ]);

                        $sub_code = random_int(1000000, 10000000);
                        $rand_code = Str::random(8);
                        $subscription = Subscription::query()->create([
                            'user_id' => $user->id,
                            'service_id' => $order->service->id,
                            'status' => "active",
                            'name' => $pre_order->service_name,
                            'code' => $sub_code,
                            'slug' => $pre_order->service_name . " - " . $sub_code,
                            "expire_at" => now()->addDays($order->service->package_duration->name),
                            'uuid' => Str::uuid(),
                            'subId' => Str::random(16)
                        ]);
                        $server_address = $order->service->server->address;
                        $order->update(["status" => "success"]);
                        $user->decrement("wallet", $order->payable_price);
                        $service = $order->service;
                        $server_type = $order->service->server->type;

                        if ($server_type == "marzban") {
                            try {
                                $res = Http::asForm()->post("$server_address/api/admin/token", [
                                    "username" => $service->server->username,
                                    "password" => $service->server->password,
                                    "grant_type" => "password"
                                ]);
                                $auth_res = $res->body();
                                $auth_res = json_decode($res->body(), true);

                                Log::debug($auth_res["access_token"]);

                                $auth_access_token = $auth_res["access_token"];

                                $settings = [
                                    "username" => $pre_order->service_name . $sub_code,
                                    "note" => "",
                                    "data_limit_reset_strategy" => "no_reset",
                                    "data_limit" => $service->package->value > 0 ? $service->package->value * pow(1024, 3) : 0,
                                    "expire" => now()->addDays($service->package_duration->name)->timestamp,
                                    "status" => "hold",
                                    "on_hold_expire_duration" =>  0,
                                    "proxies" => array(
                                        "vless" => array(
                                            "flow" => ""
                                        ),
                                        "vmess" => array(),
                                        "trojan" => array(),
                                        "shadowsocks" => array(
                                            "method" => "chacha20-ietf-poly1305"
                                        )
                                    ),
                                    "inbounds" => array(
                                        "vless" => array(
                                            "VLESS + WS + TLS",
                                            "VLESS + WS",
                                            "VLESS TCP REALITY",
                                            "VLESS GRPC REALITY"
                                        ),
                                        "vmess" => array(
                                            "VMess TCP"
                                        ),
                                        "trojan" => array(
                                            "TROJAN + WS"
                                        ),
                                        "shadowsocks" => array(
                                            "Shadowsocks TCP"
                                        )
                                    )

                                ];
                            } catch (\Throwable $th) {
                                //throw $th;
                                Log::debug($th->getMessage());
                            }



                            try {
                                $response = Http::withHeaders([
                                    'Accept' => 'application/json',
                                    'Content-Type' => 'application/json',
                                ])->withToken($auth_access_token)->post("$server_address/api/user", $settings);

                                // Log::debug($response);

                                $user_res = json_decode($response->body(), true);
                                $user_res = (object)  $user_res;


                                // Log::debug($user_res);
                                if ($response->successful()) {

                                    $sub_link = "{$server_address}$user_res->subscription_url";
                                    $sub_qrCode = QrCode::format('svg')->margin(2)->generate($sub_link);
                                    $sub_path = 'public/images/qrcodes/' . uniqid() . '.svg';
                                    Storage::put($sub_path, $sub_qrCode);
                                    $sub_qrcode = Storage::url($sub_path);
                                    // $reponse_data = [
                                    //     'link' => $sub_link,
                                    //     'sub' => $sub_link,
                                    //     'sub_qrcode' => $sub_qrcode,
                                    //     'v2ray_qrcode' => $sub_qrcode,
                                    // ];
                                    // return $this->successResponse($reponse_data, "ایجاد  با موفقیت انجام شد");
                                    $location = $order->service->server->name;
                                    $volume = $order->service->package->name;
                                    $code = $subscription->code;
                                    $expire_date = formatGregorian($subscription->expire_at);
                                    $message = "📣 * سرویس شما با موفقیت ایجاد شد*\n\n" .
                                        "💎 *کد سرویس:* `$code`\n" .
                                        "🌎 *لوکیشن:* `$location`\n" .
                                        "⏳ *تاریخ انقضا:* `$expire_date`\n" .
                                        "♾ *حجم کل:* `$volume` \n\n" .
                                        "📌 *لینک اشتراک* \n\n" .
                                        "`$sub_link` \n\n";
                                    Telegram::sendPhoto([
                                        "chat_id" => $sender->id,
                                        'photo' => InputFile::create(asset(GenerateConfigService::generateConfigQrCode($sub_link))),
                                        'caption' => $message,
                                        'reply_markup' => KeyboardHandler::home(),
                                        'parse_mode' => 'MarkdownV2',
                                        'width' => 300,
                                        'height' => 300,
                                    ]);
                                    $owner_users = User::query()->where('is_notifable', true)->get();
                                    $order_user = $user->username . " - " . $user->uid;
                                    $notif_message = "⚠️ * سرویس جدیدی با مشخصات زیر خریداری شد*\n\n" .
                                        "♾ * کاربر:* `$order_user` \n\n" .
                                        "💎 *کد سرویس:* `$code`\n" .
                                        "🌎 *لوکیشن:* `$location`\n" .
                                        "⏳ *تاریخ انقضا:* `$expire_date`\n" .
                                        "♾ *حجم کل:* `$volume` \n\n";

                                    foreach ($owner_users as $key => $owner_user) {
                                        Telegram::sendMessage([
                                            'text' => $notif_message,
                                            "chat_id" => $owner_user->uid,
                                            'parse_mode' => 'MarkdownV2',
                                            'reply_markup' => KeyboardHandler::home(),
                                        ]);
                                    }
                                }
                            } catch (\Throwable $th) {
                                Log::debug($th->getMessage());
                                Telegram::sendMessage([
                                    'text' => $th->getMessage(),
                                    "chat_id" => $sender->id,
                                    'parse_mode' => 'MarkdownV2',
                                    'reply_markup' => KeyboardHandler::home(),
                                ]);
                                // $reponse_data = [];
                                // return $this->successResponse($subscription, "خطا در ایجاد اشتراک");
                            }
                        } else {
                            $res = Http::post("$server_address/login", [
                                "username" => $order->service->server->username,
                                "password" => $order->service->server->password
                            ]);
                            $cookieJar = $res->cookies();
                            $cookiesArray = [];
                            foreach ($cookieJar as $cookie) {
                                $cookiesArray[] = $cookie->getName() . '=' . $cookie->getValue();
                            }
                            $cookiesString = implode('; ', $cookiesArray);
                            $package_duration_time = $order->service->package_duration->value > 0 ? -$order->service->package_duration->value * 24 * 60 * 60 * 1000 : 0;
                            $settings = [
                                "clients" => [
                                    [
                                        "id" => $subscription->uuid,
                                        "flow" => "",
                                        "email" => $subscription->code,
                                        "limitIp" => 0,
                                        "totalGB" => $order->service->package->value > 0 ? $order->service->package->value * pow(1024, 3) : 0,
                                        "expiryTime" => $package_duration_time,
                                        "enable" => true,
                                        "tgId" => "",
                                        "subId" => $subscription->subId
                                    ]
                                ]
                            ];
                            $server_inbound_id = $order->service->server->inbound;
                            $response = Http::withHeaders([
                                'Cookie' => $cookiesString,
                            ])->post("$server_address/panel/inbound/addClient", [
                                "id" => intval($server_inbound_id),
                                "settings" => json_encode($settings)
                            ]);
                            try {

                                $inbound = Http::withHeaders(['Cookie' => $cookiesString])->get("$server_address/xui/API/inbounds/get/$server_inbound_id");
                                $inbound_res = json_decode($inbound->body());
                                $inbound_obj = $inbound_res->obj;
                                $network = json_decode($inbound_obj->streamSettings)->network;
                                $inbound_port = $inbound_obj->port;
                                $inbound_remark = $inbound_obj->remark;
                                if ($response->successful()) {
                                    $location = $order->service->server->name;
                                    $volume = $order->service->package->name;
                                    $code = $subscription->code;
                                    $expire_date = formatGregorian($subscription->expire_at);
                                    $parts = parse_url($server_address);
                                    $clean_server_url = $parts['host'];
                                    $sub_link = GenerateConfigService::generateSubscription($subscription->id);
                                    $service_link = "vless://$subscription->uuid@$clean_server_url:$inbound_port?type=$network&path=%2F&security=none#$inbound_remark-$subscription->code";
                                    $message = "📣 * سرویس شما با موفقیت ایجاد شد*\n\n" .
                                        "💎 *کد سرویس:* `$code`\n" .
                                        "🌎 *لوکیشن:* `$location`\n" .
                                        "⏳ *تاریخ انقضا:* `$expire_date`\n" .
                                        "♾ *حجم کل:* `$volume` \n\n" .
                                        "📌 *لینک v2ray* \n\n" .
                                        "`$service_link` \n\n" .
                                        "📌 *لینک اشتراک* \n\n" .
                                        "`$sub_link` \n\n";
                                    Telegram::sendPhoto([
                                        "chat_id" => $sender->id,
                                        'photo' => InputFile::create(asset(GenerateConfigService::generateConfigQrCode($sub_link))),
                                        'caption' => $message,
                                        'reply_markup' => KeyboardHandler::home(),
                                        'parse_mode' => 'MarkdownV2',
                                        'width' => 300,
                                        'height' => 300,
                                    ]);
                                    $owner_users = User::query()->where('is_notifable', true)->get();
                                    $order_user = $user->username . " - " . $user->uid;
                                    $notif_message = "⚠️ * سرویس جدیدی با مشخصات زیر خریداری شد*\n\n" .
                                        "♾ * کاربر:* `$order_user` \n\n" .
                                        "💎 *کد سرویس:* `$code`\n" .
                                        "🌎 *لوکیشن:* `$location`\n" .
                                        "⏳ *تاریخ انقضا:* `$expire_date`\n" .
                                        "♾ *حجم کل:* `$volume` \n\n";

                                    foreach ($owner_users as $key => $owner_user) {
                                        Telegram::sendMessage([
                                            'text' => $notif_message,
                                            "chat_id" => $owner_user->uid,
                                            'parse_mode' => 'MarkdownV2',
                                            'reply_markup' => KeyboardHandler::home(),
                                        ]);
                                    }
                                }
                            } catch (\Throwable $th) {
                                // dd($th->getMessage());
                                Telegram::sendMessage([
                                    'text' => $th->getMessage(),
                                    "chat_id" => $sender->id,
                                ]);
                            }
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                        Log::debug($th->getMessage());
                    }
                }

                return true;
            } else if ($callbackData == "extension_service") {
                $extension_service = ExtensionService::query()->where('user_id', $user->id)->first();
                $extension_price = $extension_service->package_duration->price *  intval($extension_service->volume);
                $extension_sub = $extension_service->subscription;
                if ($extension_price > $user->wallet) {
                    Telegram::sendMessage([
                        'text' => "❌ موجودی شما برای خرید این سرویس کافی نمیباشد ",
                        "chat_id" => $sender->id,
                        // 'reply_markup' => $encodedMarkup,
                    ]);
                } else {
                    Telegram::sendMessage([
                        'text' => "🔄 در حال تمدید سرویس  . . .",
                        "chat_id" => $sender->id,
                    ]);
                    try {


                        $service = $extension_sub->service;
                        $server_type = $service->server->type;

                        $server_address = $extension_sub->service->server->address;
                        $extension_service->update(["status" => "success"]);


                        $user->decrement("wallet", $extension_price);

                        $added_deadline = $extension_service->package_duration->value;
                        $today = Carbon::now();
                        $givenDate = Carbon::parse($extension_sub->expire_at);

                        if ($server_type == "marzban") {
                            GenerateConfigService::extensionMarzbanService($extension_service->package_duration->id, $extension_service->volume, $extension_sub->id);
                        } else {
                            $res = Http::post("$server_address/login", [
                                "username" => $extension_sub->service->server->username,
                                "password" => $extension_sub->service->server->password
                            ]);;
                            $cookieJar = $res->cookies();
                            $cookiesArray = [];
                            foreach ($cookieJar as $cookie) {
                                $cookiesArray[] = $cookie->getName() . '=' . $cookie->getValue();
                            }
                            $cookiesString = implode('; ', $cookiesArray);
                            $inbound_obj = GenerateConfigService::getClientTraffics($extension_sub->id);
                            $volume_consumed = $inbound_obj->up + $inbound_obj->down;
                            $total = $inbound_obj->total;
                            $remaining_volume = $total - $volume_consumed;
                            $extension_service_total = $extension_service->volume * pow(1024, 3);

                            $diffInDays = $today->diffInDays($givenDate);
                            if ($givenDate->isPast()) {
                                $diffInDays = 0;
                                $new_volume = $extension_service_total;
                            } else {
                                $new_volume = $remaining_volume + $extension_service_total;
                            }
                            $package_duration_time = ($diffInDays + $added_deadline) * 24 * 60 * 60 * 1000;
                            $settings = [
                                "clients" => [
                                    [
                                        "id" => $extension_sub->uuid,
                                        "flow" => "",
                                        "email" => $extension_sub->code,
                                        "limitIp" => 0,
                                        "totalGB" => $new_volume,
                                        "expiryTime" => -$package_duration_time,
                                        "enable" => true,
                                        "tgId" => "",
                                        "subId" => $extension_sub->subId
                                    ]
                                ]
                            ];
                            $server_inbound_id = $extension_sub->service->server->inbound;
                            $response = Http::withHeaders([
                                'Cookie' => $cookiesString,
                            ])->post("$server_address/panel/inbound/updateClient/$extension_sub->uuid", [
                                "id" => intval($server_inbound_id),
                                "settings" => json_encode($settings)
                            ]);
                        }



                        $code = $extension_sub->code;
                        if ($givenDate->isPast()) {
                            Subscription::query()->where('id', $extension_sub->id)->update(
                                ['expire_at' => now()->addDays($added_deadline)]
                            );
                        } else {
                            Subscription::query()->where('id', $extension_sub->id)->update(
                                ['expire_at' => Carbon::parse($extension_sub->expire_at)->addDays($added_deadline)]
                            );
                        }

                        $message = "✅ سرویس با کد {$code} تمدید شد.";
                        Telegram::sendMessage([
                            'text' => $message,
                            "chat_id" => $sender->id,
                            'reply_markup' => KeyboardHandler::home(),
                        ]);
                    } catch (\Throwable $th) {
                        // dd($th->getMessage());
                    }
                }

                return true;
            }




            // Telegram::answerCallbackQuery([
            //     'callback_query_id' => $callbackQueryId,
            //     'text' => $responseText,
            // ]);

            return true;
        }

        if ($update->getMessage()->text !== "/start") {
            if ($update->getMessage()->text == Keyboards::HOME) {
                Telegram::sendMessage([
                    'text' => "سلام {$user->username} عزیز، به ربات ما خوش آمدید. 🚀\nیکی از دکمه های زیر را انتخاب کنید !",
                    'chat_id' => $sender->id,
                    'reply_markup' => KeyboardHandler::home(),
                ]);
                return true;
            }

            if ($update->getMessage()->text == Keyboards::PURCHASE_SERVICE) {
                $servers = Server::query()->where('is_active', true)->get();
                if (count($servers) == 0) {

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
                    $user->update([
                        'section' => Keyboards::PURCHASE_SERVICE,
                        'step' => 2
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
                    array_push($keyboards, [['text' => Keyboards::HOME]]);

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
            if ($update->getMessage()->text == Keyboards::CHARGE) {
                $user->update([
                    'section' => Keyboards::CHARGE,
                    'step' => 1
                ]);
                $payment_methods = PaymentMethod::query()->where('status', 'active')->get();
                $keyboards = [];
                $keyboards_keyboards = $payment_methods->chunk(1);
                foreach ($keyboards_keyboards as $chunk) {
                    $row = [];
                    foreach ($chunk as $method) {
                        $row[] = ['text' => $method->title, 'callback_data' => $method->type];
                    }
                    $keyboards[] = $row;
                }
                $keyboardMarkup = [
                    'inline_keyboard' => $keyboards,
                ];
                $encodedMarkup = json_encode($keyboardMarkup);
                Telegram::sendMessage([
                    'text' => "💳 روش پرداخت را انتخاب کنید:",
                    "chat_id" => $sender->id,
                    'reply_markup' => $encodedMarkup,
                ]);
                return true;
            }
            if ($update->getMessage()->text == Keyboards::PROFILE) {
                $user->update([
                    'section' => Keyboards::PROFILE,
                    'step' => 1
                ]);
                $services = $user->subscriptions()->get()->count();
                $avaible_services = $user->subscriptions()->where('status', 'active')->whereDate('expire_at', '>=', now())->get()->count();
                $register_date = formatGregorian($user->created_at);
                $message = "👤 *شناسه کاربری:* `$user->uid`\n\n" .
                    "⏰ *تاریخ عضویت:* `$register_date`\n\n" .
                    "💰 *موجودی:* `$user->wallet` " . "تومان\n\n" .
                    "🗳 *تعداد کل سرویس ها:* `$services`\n\n" .
                    "✅ *سرویس های فعال:* `$avaible_services`\n\n";
                Telegram::sendMessage([
                    'text' => $message,
                    "chat_id" => $sender->id,
                    'parse_mode' => 'MarkdownV2',
                    'reply_markup' => KeyboardHandler::home(),
                ]);
            }
            if ($update->getMessage()->text == Keyboards::GUIDE) {
                $platforms = GuidePlatform::query()->get();
                $keyboards = [];
                $keyboards_keyboards = $platforms->chunk(2);
                foreach ($keyboards_keyboards as $chunk) {
                    $row = [];
                    foreach ($chunk as $platform) {
                        $row[] = ['text' => $platform->name];
                    }
                    $keyboards[] = $row;
                }
                array_push($keyboards, [['text' => Keyboards::HOME]]);
                $replyMarkup = [
                    'keyboard' => $keyboards,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => false,
                ];
                $encodedMarkup = json_encode($replyMarkup);
                Telegram::sendMessage([
                    'text' => "🖥 سیستم عامل خود را انتخاب کنید: ",
                    'chat_id' => $sender->id,
                    'reply_markup' => $encodedMarkup,
                ]);
                $user->update([
                    'section' => Keyboards::GUIDE,
                    'step' => 1
                ]);
            }
            if ($update->getMessage()->text == Keyboards::SUPPORT) {
                Telegram::sendMessage([
                    'text' => "📞 پیام خود را در قالب یک پیام جهت برسی مشکل ارسال کنید : ",
                    'chat_id' => $sender->id,
                ]);
                $user->update([
                    'section' => Keyboards::SUPPORT,
                    'step' => 1
                ]);
                return true;
            }
            if ($update->getMessage()->text == Keyboards::PRICING) {
                $pricing_exists = Pricing::query()->where('is_default', true)->first();
                if (!is_null($pricing_exists)) {

                    $pricing_content = $pricing_exists->content;
                } else {
                    $pricing_content = "متن تعرفه هنوز تنظیم نشده است.";
                }
                Telegram::sendMessage([
                    'text' => $pricing_content,
                    'chat_id' => $sender->id,
                ]);
                $user->update([
                    'section' => Keyboards::SUPPORT,
                    'step' => 1
                ]);
                return true;
            }
            if ($update->getMessage()->text == Keyboards::SERVICES) {
                $user_services = Subscription::query()->where('user_id', $user->id)->where("status", "active")->get();
                $keyboards = [];
                $keyboards_keyboards = $user_services->chunk(2);
                foreach ($keyboards_keyboards as $chunk) {
                    $row = [];
                    foreach ($chunk as $service) {
                        $row[] = ['text' => $service->slug];
                    }
                    $keyboards[] = $row;
                }
                array_push($keyboards, [['text' => Keyboards::HOME]]);
                $replyMarkup = [
                    'keyboard' => $keyboards,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => false,
                ];
                $encodedMarkup = json_encode($replyMarkup);
                Telegram::sendMessage([
                    'text' => "🗂 سرویس مورد نظر رو انتخاب کنید:",
                    'chat_id' => $sender->id,
                    'reply_markup' => $encodedMarkup,
                ]);
                $user->update([
                    'section' => Keyboards::SERVICES,
                    'step' => 1
                ]);
                return true;
            }

            $servers = Server::query()->pluck('name')->toArray();
            $durations = PackageDuration::query()->pluck('name')->toArray();
            $packages = Package::query()->pluck('name')->toArray();
            $platforms = GuidePlatform::query()->pluck('name')->toArray();
            $platform_clients = GuidePlatformClient::query()->pluck('name')->toArray();


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
                    array_push($keyboards, [['text' => Keyboards::HOME]]);

                    $replyMarkup = [
                        'keyboard' => $keyboards,
                        'resize_keyboard' => true,
                        'one_time_keyboard' => false,
                    ];
                    $encodedMarkup = json_encode($replyMarkup);

                    Telegram::sendMessage([
                        'text' => "⏳ مدت زمان (تعداد روز)  سرویس را  انتخاب کنید:",
                        "chat_id" => $sender->id,
                        'reply_markup' => $encodedMarkup,
                    ]);
                    $user->update([
                        'section' => Keyboards::PURCHASE_SERVICE,
                        'step' => 2
                    ]);
                } else {
                }
            } else if (in_array($update->getMessage()->text, $durations) && $user->section == Keyboards::PURCHASE_SERVICE) {
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
                    array_push($keyboards, [['text' => Keyboards::HOME]]);
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
                    // $service = Service::query()
                    //     ->where('server_id', $pre_order->server_id)
                    //     ->where('package_duration_id', $pre_order->package_duration_id)
                    //     ->where('package_id', $selected_package->id)
                    //     ->where('status', "active")
                    //     ->first();
                    $condition_config = true;
                    if ($condition_config) {

                        $message = " 📝 یک نام دلخواه برای این سرویس وارد کنید: (اختیاری)\n\n" .
                            "📌 نام باید دارای شرایط زیر باشد:\n" .
                            "1⃣ می تواند با $#@ و اعداد و حروف انگلیسی شروع شود. \n" .
                            "2⃣ بین کاراکترها می توان از $#@-_/ و فاصله استفاده کرد. \n" .
                            "3⃣ انتهای نام نمی تواند شامل $#@-_/ و فاصله باشد.\n" .
                            "4️⃣ طول نامی که انتخاب می کنید نمی تواند بیش تر از ۱۱ کاراکتر باشد. \n";
                        Telegram::sendMessage([
                            'text' => $message,
                            "chat_id" => $sender->id,
                            // 'reply_markup' => $encodedKeyboard,
                        ]);

                        $user->update([
                            'section' => Keyboards::PURCHASE_SERVICE,
                            'step' => 4
                        ]);
                    } else {
                        Telegram::sendMessage([
                            'text' => "ظرفیت سرور موردنظر تکمیل شده و یا در دسترس نمی باشد",
                            "chat_id" => $sender->id,
                            // 'reply_markup' => $encodedKeyboard,
                        ]);
                    }
                }
            } else if ($user->step == "4" && $user->section == Keyboards::PURCHASE_SERVICE) {
                $pre_order = PreOrder::query()->where('user_id', $user->id)->first();
                $pre_order->update([
                    'service_name' => $update->getMessage()->text
                ]);
                $service = Service::query()
                    ->where('server_id', $pre_order->server_id)
                    ->where('package_duration_id', $pre_order->package_duration_id)
                    ->where('package_id', $pre_order->package_id)
                    ->where('status', "active")
                    ->first();
                if (!is_null($service)) {
                    $order = Order::query()->create([
                        "user_id" => $user->id,
                        "service_id" => $service->id,
                        "status" => "pending",
                        "payable_price" => $service->price,
                        "price" => $service->price,
                        "type" => "purchase"
                    ]);
                    $location = $service->server->name;
                    $volume = $service->package->name;
                    $date = $service->package_duration->name;
                    $trackingCode = $order->reference_code;
                    $price = round($service->price);
                    $amount = "{$price} تومان";
                    $message = "ℹ️ فاکتور شما با جزئیات زیر با موفقیت ساخته شد.\n\n" .
                        "⬅️ لوکیشن : $location\n" .
                        "⬅️ حجم : $volume\n" .
                        "⬅️ تاریخ : $date\n" .
                        "⬅️ کد پیگیری : $trackingCode\n\n" .
                        "💸 مبلغ سرویس شما : $amount\n\n" .
                        " 👇🏻 در صورت تایید اطلاعات بالا میتوانید از طریق دکمه های زیر پرداخت خود را انجام بدید.";

                    // $res = Http::post("https://panel.aqayepardakht.ir/api/v2/create", [
                    //     "pin" => "sandbox",
                    //     "amount" => $order->price,
                    //     "callback" => "https://pashmak-titab.store/api/client/payment/callback",
                    // ]);
                    // $dd = json_decode($res->body());
                    // $transid = $dd->transid;
                    $inlineKeyboard = [
                        [
                            [
                                'text' => '💰 کیف پول',
                                'callback_data' => "purchase_wallet"
                            ],
                        ],
                    ];
                    $encodedKeyboard = json_encode(['inline_keyboard' => $inlineKeyboard]);
                    $user->update([
                        'section' => Keyboards::PURCHASE_SERVICE,
                        'step' => 5
                    ]);
                    Telegram::sendMessage([
                        'text' => $message,
                        "chat_id" => $sender->id,
                        'reply_markup' => $encodedKeyboard,
                    ]);
                } else {
                    Telegram::sendMessage([
                        'text' => "ظرفیت سرور موردنظر تکمیل شده و یا در دسترس نمی باشد",
                        "chat_id" => $sender->id,
                        // 'reply_markup' => $encodedKeyboard,
                    ]);
                    return true;
                }
            } else if ($user->step == "2" && $user->section == Keyboards::CHARGE) {

                $wallet_trans = WalletTransaction::query()->where('user_id', $user->id)->first();
                $payment_method = PaymentMethod::query()->where('type', $wallet_trans->method)->first();
                if ($wallet_trans->method == "card") {
                    $amount = $update->getMessage()->text;
                    $wallet_trans->update(["amount" => $amount]);
                    $wallet_amount = $wallet_trans->amount;
                    $payment = Payment::query()->create([
                        "paymentable_type" => User::class,
                        "paymentable_id" => $user->id,
                        "user_id" => $user->id,
                        "payment_method_id" => $payment_method->id,
                        "amount" => $amount,
                        "status" => "pending",
                    ]);
                    Telegram::sendMessage([
                        'text' => "🔄 در حال ساخت فاکتور شما . . .",
                        "chat_id" => $sender->id,
                    ]);
                    $card_num = settingRepo()->get("card_number");
                    $card_name = json_decode(settingRepo()->get("card_name"), true);
                    $invoise_code = $payment->reference_code;
                    $newMessageText = "📣 *فاکتور شما با موفقیت ساخته شد*\n\n" .
                        "💎 * شماره فاکتور:* `$invoise_code`\n" .
                        "💳 * مبلغ قابل پرداخت:* `$wallet_amount` " . "تومان\n" .
                        "🔢 * شماره کارت :* `$card_num` * $card_name *\n" .
                        "👇🏻مبلغ مورد نظر را به شماره کارت بالا واریز کنید و سپس رسید  خود را در همین قسمت ارسال کنید ";
                    $user->update([
                        'section' => Keyboards::CHARGE,
                        'step' => 3
                    ]);

                    Telegram::sendMessage([
                        "chat_id" => $sender->id,
                        'text' => $newMessageText,
                        'parse_mode' => 'MarkdownV2',
                    ]);
                    return true;
                } else if ($wallet_trans->method == "voucher") {
                    $ev_number = $update->getMessage()->text;
                    if (strlen($ev_number) === 10) {
                        Telegram::sendMessage([
                            'text' => "✅ کد فعالسازی ووچر را ارسال نمایید:",
                            "chat_id" => $sender->id,
                        ]);
                        VoucherTransaction::query()->where('user_id', $user->id)->delete();
                        VoucherTransaction::query()->create([
                            'user_id' => $user->id,
                            "ev_number" => $ev_number,
                            "status" => "pending"
                        ]);
                        $user->update([
                            'section' => Keyboards::CHARGE,
                            'step' => 3
                        ]);
                        return true;
                    } else {
                        Telegram::sendMessage([
                            'text' => "⛔️ کد ووچر الکترونیکی یک کد ۱۰ رقمی است.",
                            "chat_id" => $sender->id,
                        ]);
                        return true;
                    }
                }
            } else if ($user->step == "3" && $user->section == Keyboards::CHARGE) {
                $wallet_trans = WalletTransaction::query()->where('user_id', $user->id)->first();
                if ($wallet_trans->method == "card") {
                    if (isset($update->getMessage()->photo) && $update->getMessage()->photo) {
                        if (!is_null($wallet_trans) && $wallet_trans->method == "card") {
                            $dd = Telegram::getFile(['file_id' => $update->getMessage()->photo[2]->file_id]);
                            $filePath = $dd->getFilePath();
                            $token = getenv("TELEGRAM_BOT_TOKEN");
                            $contents = file_get_contents('https://api.telegram.org/file/bot' . $token . '/' . $filePath);
                            $storagePath = 'receipts/';
                            Storage::put("public/" . $storagePath . $filePath, $contents, 'public');
                            $latest_payment = Payment::query()->where('user_id', $user->id)->latest()->first();
                            $latest_payment->update(['receipt' => asset('storage/' . $storagePath . $filePath), 'status' => "pending_confirmation"]);
                            try {
                                Telegram::sendMessage([
                                    "chat_id" => $sender->id,
                                    'text' => "✅ فیش ارسالی شما با موفقیت به مدیریت ارسال شد پس از برسی حساب شما به صورت خودکار شارژ خواهد شد !",
                                ]);
                            } catch (\Throwable $th) {
                                //throw $th;
                            }
                            $owner_users = User::query()->where('is_notifable', true)->get();
                            $formated_payment_price = number_format(round($latest_payment->amount));
                            $payment_reference_code = $latest_payment->reference_code;
                            $payment_date = formatGregorian($latest_payment->created_at);
                            $payment_user = $latest_payment->user->username . " " . $latest_payment->user->uid;
                            $message = "💵  مبلغ: {$formated_payment_price} تومان \n" .
                                "🌿  کد پیگیری: {$payment_reference_code}  \n" .
                                "📆   تاریخ: {$payment_date}  \n" .
                                "📌   کاربر: {$payment_user}  \n" .
                                " 👇🏻درخواست تایید رسید کارت به کارت با مشخصات  بالا  در  انتظار تایید رسید پرداخت می باشد. لطفا جهت بررسی آن اقدام کنید";
                            $inlineKeyboard = [
                                [
                                    [
                                        'text' => 'مشاهده تراکنش',
                                        //"https://pashmak-titab.store/panel/payments/edit/$latest_payment->id"
                                        'url' => "https://pashmak-titab.store/panel/payments/edit/$latest_payment->id"
                                    ],
                                ],
                            ];
                            $encodedKeyboard = json_encode(['inline_keyboard' => $inlineKeyboard]);
                            foreach ($owner_users as $key => $owner_user) {
                                try {
                                    Telegram::sendMessage([
                                        'text' => $message,
                                        "chat_id" => $owner_user->uid,
                                        'reply_markup' => $encodedKeyboard,
                                    ]);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                }
                            }
                            return true;
                        }
                    } else {
                        try {
                            Telegram::sendMessage([
                                "chat_id" => $sender->id,
                                'text' => "❌ ورودی فقط باید عکس باشد !",
                            ]);
                        } catch (\Throwable $th) {
                            //throw $th;
                        }
                        return true;
                    }
                } else if ($wallet_trans->method == "voucher") {
                    $ev_code = $update->getMessage()->text;
                    if (strlen($ev_code) === 16) {
                        $voucher_transaction = VoucherTransaction::query()->where('user_id', $user->id)->first();
                        $voucher_transaction->update([
                            "ev_code" => $ev_code
                        ]);
                        try {
                            $voucher_account_id = settingRepo()->get("voucher_account_id");
                            $voucher_account_pass = settingRepo()->get("voucher_pass");
                            $voucher_account_payee = settingRepo()->get("voucher_payee_account");
                            $res = Http::asForm()->post("https://perfectmoney.com/acct/ev_activate.asp", [
                                "AccountID" => $voucher_account_id,
                                "PassPhrase" => $voucher_account_pass,
                                "Payee_Account" => $voucher_account_payee,
                                "ev_number" => $voucher_transaction->ev_number,
                                "ev_code" => $ev_code,
                            ]);
                            $html = $res->body();
                            $dom = new DOMDocument();
                            libxml_use_internal_errors(true);
                            $dom->loadHTML($html);
                            libxml_clear_errors();
                            $xpath = new DOMXPath($dom);
                            $error_node = $xpath->query('//input[@name="ERROR"]')->item(0);
                            $voucher_amount_node = $xpath->query('//input[@name="VOUCHER_AMOUNT"]')->item(0);
                            $voucher_amount_currency_node = $xpath->query('//input[@name="VOUCHER_AMOUNT_CURRENCY"]')->item(0);
                            // if (!is_null($error_node)) {
                            //     Telegram::sendMessage([
                            //         'text' => $error_node->getAttribute('value'),
                            //         "chat_id" => $sender->id,
                            //     ]);
                            //     return true;
                            // }
                            if (!is_null($voucher_amount_node)) {
                                $voucher_amount = $voucher_amount_node->getAttribute('value');
                                $voucher_amount_currency = $voucher_amount_currency_node->getAttribute('value');
                                $dollar_price = intval(settingRepo()->get("usd_amount"));
                                $wallet_amount = $voucher_amount * $dollar_price;

                                $payment_method = PaymentMethod::query()->where('type', "voucher")->first();
                                $payment = Payment::query()->create([
                                    "paymentable_type" => User::class,
                                    "paymentable_id" => $user->id,
                                    "user_id" => $user->id,
                                    "payment_method_id" => $payment_method->id,
                                    "amount" => $wallet_amount,
                                    "status" => "success",
                                ]);
                                $user->increment("wallet", $wallet_amount);
                                $reference_code = $payment->reference_code;
                                $wallet_amount =  number_format($wallet_amount);
                                $newMessageText = "✅ پرداخت موفق\n\n" .
                                    "📮 شناسه سفارش: $reference_code\n" .
                                    "💰مبلغ $voucher_amount دلار معادل $wallet_amount تومان به حساب شما افزوده شد \n";

                                Telegram::sendMessage([
                                    "chat_id" => $sender->id,
                                    'text' => $newMessageText,
                                    // 'parse_mode' => 'MarkdownV2',
                                ]);
                                return true;
                            } else {
                                Telegram::sendMessage([
                                    'text' => "⛔️ کد ووچر یا کد فعالسازی نامعتبر است",
                                    "chat_id" => $sender->id,
                                ]);
                                return true;
                            }
                        } catch (\Throwable $th) {
                            Telegram::sendMessage([
                                'text' => $th->getMessage(),
                                "chat_id" => $sender->id,
                            ]);
                        }
                    } else {
                        Telegram::sendMessage([
                            'text' => "⛔️ کد فعالسازی ووچر یک کد ۱۶ رقمی است.",
                            "chat_id" => $sender->id,
                        ]);
                        return true;
                    }
                }
            } else if (in_array($update->getMessage()->text, $platforms)) {
                $selected_platform = GuidePlatform::query()->where('name', $update->getMessage()->text)->first();
                $clients = GuidePlatformClient::query()->where('guide_platform_id', $selected_platform->id)->get();
                $keyboards = [];
                $keyboards_keyboards = $clients->chunk(2);
                foreach ($keyboards_keyboards as $chunk) {
                    $row = [];
                    foreach ($chunk as $duration) {
                        $row[] = ['text' => $duration->name];
                    }
                    $keyboards[] = $row;
                }
                array_push($keyboards, [['text' => Keyboards::HOME]]);
                $replyMarkup = [
                    'keyboard' => $keyboards,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => false,
                ];
                $encodedMarkup = json_encode($replyMarkup);

                Telegram::sendMessage([
                    'text' => "⁉️ با کدوم نرم افزار می خواهید به سرویس متصل بشید؟",
                    "chat_id" => $sender->id,
                    'reply_markup' => $encodedMarkup,
                ]);
                $user->update([
                    'section' => Keyboards::GUIDE,
                    'step' => 2
                ]);
            } else if (in_array($update->getMessage()->text, $platform_clients)) {
                $selected_client = GuidePlatformClient::query()->where('name', $update->getMessage()->text)->first();
                $platform = $selected_client->guide_platform->name;
                $video_path = asset($selected_client->video);
                $markdownText = "لینک آموزش  برنامه استفاده شده : [$selected_client->name]($video_path)\n\n" .
                    "📚 آموزش اتصال در $platform با $selected_client->name\n📌 [لینک دانلود نرم افزارهای استفاده شده در این آموزش: $selected_client->name]($selected_client->link)";
                // Telegram::sendVideo([
                //     "chat_id" => $sender->id,
                //     "video" => InputFile::create(public_path($selected_client->video)),
                //     'parse_mode' => 'MarkdownV2',
                //     'caption' => $markdownText,
                //     'width' => 1280,
                //     'height' => 720,

                // ]);
                Telegram::sendMessage([
                    'text' => $markdownText,
                    'chat_id' => $sender->id,
                    'parse_mode' => 'MarkdownV2',
                ]);
                $user->update([
                    'section' => Keyboards::GUIDE,
                    'step' => 3
                ]);
            } else if ($user->step == "1" && $user->section == Keyboards::SUPPORT) {
                try {
                    $message = SupportMessage::query()->create([
                        'user_id' => $user->id,
                        'message' => $update->getMessage()->text,
                        'status' => "pending"
                    ]);

                    Telegram::sendMessage([
                        'text' => "✅ پیام شما با موفقیت به ادمین های ربات ارسال شد !",
                        'chat_id' => $sender->id,
                    ]);

                    $owner_users = User::query()->where('is_notifable', true)->get();
                    // Telegram::sendMessage([
                    //     'text' => json_encode($owner_users),
                    //     'chat_id' => $sender->id,
                    // ]);
                    // return true;
                    $notif_message = "پیام جدیدی توسط کاربر {$user->username} ایجاد شد";
                    $inlineKeyboard = [
                        [
                            [
                                'text' => 'پاسخ به پیام',
                                'url' => "https://pashmak-titab.store/panel/support/messages/edit/$message->id"
                            ],
                        ],
                    ];
                    $encodedKeyboard = json_encode(['inline_keyboard' => $inlineKeyboard]);
                    foreach ($owner_users as $key => $owner_user) {
                        Telegram::sendMessage([
                            'text' => $notif_message,
                            "chat_id" => $owner_user->uid,
                            'reply_markup' => $encodedKeyboard,
                        ]);
                    }

                    $user->update([
                        'section' => Keyboards::SUPPORT,
                        'step' => 2
                    ]);
                    return true;
                } catch (\Throwable $th) {
                    return true;
                }
            } else if ($user->step == "1" && $user->section == Keyboards::SERVICES) {
                $user_sub = Subscription::query()->where('user_id', $user->id)->where('slug', $update->getMessage()->text)->first();
                if (is_null($user_sub)) {
                    Telegram::sendMessage([
                        'text' => "⛔️ سرور انتخاب شده نامعتبر می  باشد",
                        'chat_id' => $sender->id,
                    ]);
                } else {
                    try {
                        Telegram::sendMessage([
                            'text' => "🔄 در حال پردازش سرویس شما . . .",
                            "chat_id" => $sender->id,
                        ]);
                        ExtensionService::query()->updateOrCreate([
                            'user_id' => $user->id
                        ], [
                            'status' => "pending",
                            'subscription_id' => $user_sub->id
                        ]);
                        $service = $user_sub->service;
                        $server_type = $service->server->type;
                        $server_address = $service->server->address;

                        if ($server_type == "marzban") {

                            $res = Http::asForm()->post("$server_address/api/admin/token", [
                                "username" => $service->server->username,
                                "password" => $service->server->password,
                                "grant_type" => "password"
                            ]);

                            $auth_res = json_decode($res->body());

                            $auth_access_token = $auth_res->access_token;

                            $sub_username = $user_sub->code;

                            $res = Http::withHeaders([
                                'Accept' => 'application/json',
                                'Content-Type' => 'application/json',
                            ])->withToken($auth_access_token)->get("$server_address/api/user/{$sub_username}");
                            $user_res = json_decode($res->body());
                            $location = $user_sub->service->server->name;

                            $volume_consumed = round($user_res->used_traffic / 1024 / 1024 / 1024);

                            $total = round($user_res->data_limit / 1024 / 1024 / 1024);

                            $remaining_volume = $total - $volume_consumed;

                            $remaining_volume = str_replace('.', '\.', $remaining_volume);

                            $expire_date =  formatGregorian($user_sub->expire_at);

                            $sub_link = "{$server_address}$user_res->subscription_url";


                            $message = "💎 *کد سرویس:* `$sub_username`\n" .
                                "🌎 *لوکیشن:* `$location`\n" .
                                "⏳ *تاریخ انقضا:* `$expire_date`\n" .
                                "♾ *حجم کل:* `$total` گیگابایت \n" .
                                "📊 حجم مصرف شده: {$volume_consumed} گیگابایت\n" .
                                "🧮 حجم باقی مانده: {$remaining_volume} گیگابایت\n\n" .
                                "📌 *لینک اشتراک* \n\n" .
                                "`$sub_link` \n\n";
                        } else {

                            $location = $user_sub->service->server->name;
                            $volume = $user_sub->service->package->name;
                            $service_link = GenerateConfigService::generate($user_sub->id);
                            $sub_link = GenerateConfigService::generateSubscription($user_sub->id);
                            $code = $user_sub->code;
                            $inbound_obj = GenerateConfigService::getClientTraffics($user_sub->id);
                            $volume_consumed = round(($inbound_obj->up + $inbound_obj->down) / 1024 / 1024 / 1024);
                            $total = round($inbound_obj->total / 1024 / 1024 / 1024);
                            $remaining_volume = $total - $volume_consumed;
                            $remaining_volume = str_replace('.', '\.', $remaining_volume);
                            $volume_consumed = str_replace('.', '\.', $volume_consumed);
                            $total = str_replace('.', '\.', $total);
                            $expire_date =  formatGregorian($user_sub->expire_at);

                            $message = "💎 *کد سرویس:* `$code`\n" .
                                "🌎 *لوکیشن:* `$location`\n" .
                                "⏳ *تاریخ انقضا:* `$expire_date`\n" .
                                "♾ *حجم کل:* `$total` گیگابایت \n" .
                                "📊 حجم مصرف شده: {$volume_consumed} گیگابایت\n" .
                                "🧮 حجم باقی مانده: {$remaining_volume} گیگابایت\n\n" .
                                "📌 *لینک v2ray* \n\n" .
                                "`$service_link` \n\n" .
                                "📌 *لینک اشتراک* \n\n" .
                                "`$sub_link` \n\n";
                        }


                        Telegram::sendPhoto([
                            "chat_id" => $sender->id,
                            'photo' => InputFile::create(asset(GenerateConfigService::generateConfigQrCode($sub_link))),
                            'caption' => $message,
                            'reply_markup' => KeyboardHandler::service(),
                            'parse_mode' => 'MarkdownV2',
                            'width' => 300,
                            'height' => 300,
                        ]);
                        // Telegram::sendMessage([
                        //     'text' => $message,
                        //     'chat_id' => $sender->id,
                        //     'parse_mode' => 'MarkdownV2',
                        //     'reply_markup' => KeyboardHandler::service(),
                        // ]);
                        $user->update([
                            'section' => Keyboards::SERVICES,
                            'step' => 2
                        ]);
                        return true;
                    } catch (\Throwable $th) {
                        //throw $th;
                        return true;
                    }
                }
                return true;
            } else if ($user->step == "2" && $user->section == Keyboards::SERVICES && $update->getMessage()->text == Keyboards::EXTENSION_SERVICE) {
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
                array_push($keyboards, [['text' => Keyboards::HOME]]);

                $replyMarkup = [
                    'keyboard' => $keyboards,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => false,
                ];
                $encodedMarkup = json_encode($replyMarkup);
                $user->update([
                    'section' => Keyboards::SERVICES,
                    'step' => 3
                ]);
                Telegram::sendMessage([
                    'text' => "⏳ مدت زمان تمدید سرویس را انتخاب کنید:",
                    "chat_id" => $sender->id,
                    'reply_markup' => $encodedMarkup,
                ]);
                // Telegram::sendMessage([
                //     'text' => "⛔️ سرور انتخاب شده نامعتبر می  باشد",
                //     'chat_id' => $sender->id,
                // ]);
            } elseif ($user->step == "3" && $user->section == Keyboards::SERVICES) {
                $selected_duration  = PackageDuration::query()->where('name', $update->getMessage()->text)->first();
                ExtensionService::query()->where('user_id', $user->id)->first()->update([
                    'package_duration_id' => $selected_duration->id
                ]);
                $user->update([
                    'section' => Keyboards::SERVICES,
                    'step' => 4
                ]);
                Telegram::sendMessage([
                    'text' => "♾ حجم تمدید سرویس را وارد کنید: (حداقل:  گیگابایت)",
                    "chat_id" => $sender->id,
                    // 'reply_markup' => $encodedMarkup,
                ]);
            } elseif ($user->step == "4" && $user->section == Keyboards::SERVICES) {
                Telegram::sendMessage([
                    'text' => "🔄 در حال پردازش سرویس شما . . .",
                    "chat_id" => $sender->id,
                ]);

                try {
                    $extension_service = ExtensionService::query()->where('user_id', $user->id)->first();
                    $extension_service->update([
                        'volume' => intval($update->getMessage()->text)
                    ]);
                    $sub = $extension_service->subscription;
                    $added_deadline = $extension_service->package_duration->value;
                    $added_volume = $extension_service->volume;
                    $extension_price = number_format($extension_service->package_duration->price *  intval($added_volume));
                    $user_wallet = number_format($user->wallet);

                    $service = $sub->service;
                    $server_type = $service->server->type;
                    $server_address = $service->server->address;
                    $today = Carbon::now();
                    $givenDate = Carbon::parse($sub->expire_at);
                    $diffInDays = $today->diffInDays($givenDate);

                    if ($server_type == "marzban") {


                        $res = Http::asForm()->post("$server_address/api/admin/token", [
                            "username" => $service->server->username,
                            "password" => $service->server->password,
                            "grant_type" => "password"
                        ]);

                        $auth_res = json_decode($res->body());

                        $auth_access_token = $auth_res->access_token;

                        $sub_username = $sub->code;

                        $res = Http::withHeaders([
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ])->withToken($auth_access_token)->get("$server_address/api/user/{$sub_username}");
                        $user_res = json_decode($res->body());
                        $volume_consumed = round(($user_res->used_traffic) / 1024 / 1024 / 1024);
                        $total = round($user_res->data_limit / 1024 / 1024 / 1024);
                        $remaining_volume = $total - $volume_consumed;
                    } else {

                        $inbound_obj = GenerateConfigService::getClientTraffics($sub->id);
                        $volume_consumed = round(($inbound_obj->up + $inbound_obj->down) / 1024 / 1024 / 1024);
                        $total = round($inbound_obj->total / 1024 / 1024 / 1024);
                        $remaining_volume = $total - $volume_consumed;
                    }
                    if ($givenDate->isPast()) {
                        $diffInDays = 0;
                        $new_volume = $added_volume;
                    } else {
                        $new_volume = $remaining_volume + $added_volume;
                    }
                    $new_time = $added_deadline + $diffInDays;





                    $message = "🌿 کد سرویس: {$sub->code} \n" .
                        "📆 مهلت اضافه شده: {$added_deadline} روز \n" .
                        "⏳ زمان باقی مانده: {$diffInDays} روز \n" .
                        "🌝 زمان جدید: {$new_time} روز \n" .
                        "➕ حجم اضافه شده: {$added_volume} گیگابایت \n" .
                        "🧮 حجم باقی مانده: {$remaining_volume} گیگابایت \n" .
                        "♾ حجم جدید: {$new_volume} گیگابایت \n" .
                        "💵 قیمت تمدید: {$extension_price} تومان \n" .
                        "💰 موجودی حساب شما: {$user_wallet} تومان \n" .
                        " 👇🏻 در صورت تایید اطلاعات بالا میتوانید از طریق دکمه های زیر پرداخت خود را انجام بدید.";
                    $inlineKeyboard = [
                        [
                            [
                                'text' => '💰 کیف پول',
                                'callback_data' => "extension_service"
                            ],
                        ],
                    ];
                    $encodedKeyboard = json_encode(['inline_keyboard' => $inlineKeyboard]);

                    Telegram::sendMessage([
                        'text' => $message,
                        "chat_id" => $sender->id,
                        'reply_markup' => $encodedKeyboard,
                    ]);
                    return true;
                } catch (\Throwable $th) {
                    return true;
                }
            }
        }

        return true;
    }
}
