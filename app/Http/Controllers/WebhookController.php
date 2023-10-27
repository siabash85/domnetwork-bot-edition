<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\User\Entities\User;
use Modules\Order\Entities\Order;
use Modules\Server\Entities\Server;
use App\Telegram\Keyboard\Keyboards;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Modules\Order\Entities\PreOrder;
use Modules\Server\Entities\Package;
use Modules\Server\Entities\Service;
use Modules\Payment\Entities\Payment;
use App\Telegram\Keyboard\KeyboardHandler;
use Modules\Guide\Entities\GuidePlatform;
use Modules\Guide\Entities\GuidePlatformClient;
use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\Payment\Entities\PaymentMethod;
use Modules\Server\Entities\PackageDuration;
use Modules\Server\Entities\Pricing;
use Modules\Server\Entities\Subscription;
use Modules\Support\Entities\SupportMessage;
use Modules\User\Entities\WalletTransaction;
use Telegram\Bot\FileUpload\InputFile;

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
            $responseText = 'You clicked the button with callback data: ' . $callbackData;
            $wallet_trans = WalletTransaction::query()->where('user_id', $user->id)->first();
            $wallet_amount  = $wallet_trans->amount;
            Telegram::answerCallbackQuery([
                'callback_query_id' => $callbackQueryId,
                'text' => $responseText,
            ]);
            $payment_method = PaymentMethod::query()->where('is_default', true)->first();

            $res = Http::post("https://panel.aqayepardakht.ir/api/v2/create", [
                "pin" => "sandbox",
                "amount" => $wallet_amount,
                "callback" => "https://pashmak-titab.store/api/client/wallet/payment/callback",
            ]);
            $dd = json_decode($res->body());
            $transid = $dd->transid;
            $payment =  Payment::query()->create([
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
                $keyboards = [
                    [
                        ['text' => Keyboards::HOME],
                    ],
                ];
                $replyMarkup = [
                    'keyboard' => $keyboards,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => false,
                ];
                $encodedMarkup = json_encode($replyMarkup);
                Telegram::sendMessage([
                    'text' => "💸 لطفا مبلغی که میخواهید شارژ کنید را به لاتین حداقل 2,000 تومان ارسال کنید :",
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
                $user_services = Subscription::query()->where('user_id', $user->id)->get();
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
                    $service = Service::query()
                        ->where('server_id', $pre_order->server_id)
                        ->where('package_duration_id', $pre_order->package_duration_id)
                        ->where('package_id', $selected_package->id)
                        ->where('status', "active")
                        ->first();
                    if (!is_null($service)) {

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
                $order =   Order::query()->create([
                    "user_id" => $user->id,
                    "service_id" => $service->id,
                    "status" =>  "pending",
                    "payable_price" =>  $service->price,
                    "price" =>  $service->price,
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
                $user->update([
                    'section' => Keyboards::PURCHASE_SERVICE,
                    'step' => 5
                ]);
                Telegram::sendMessage([
                    'text' => $message,
                    "chat_id" => $sender->id,
                    'reply_markup' => $encodedKeyboard,
                ]);
            } else if ($user->step == "1" && $user->section == Keyboards::CHARGE) {
                $amount = $update->getMessage()->text;
                $wallet_trans = WalletTransaction::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'user_id' => $user->id,
                        'amount' => $amount,
                        'status' => "pending"
                    ]
                );
                $keyboard = [
                    [
                        ['text' => 'درگاه پرداخت', 'callback_data' => 'online_purchase'],
                    ],

                ];
                $keyboardMarkup = [
                    'inline_keyboard' => $keyboard,
                ];
                $replyMarkup = json_encode($keyboardMarkup);


                $message = "💵 *یکی از روش های پرداخت زیر را جهت شارژ حساب خود انتخاب کنید :*\n\n" .
                    "🔢 * مبلغ:* `$amount` "  . "تومان\n";
                Telegram::sendMessage([
                    'text' => $message,
                    "chat_id" => $sender->id,
                    'parse_mode' => 'MarkdownV2',
                    'reply_markup' => $replyMarkup,

                ]);
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
                $markdownText = "📚 آموزش اتصال در $platform با $selected_client->name\n📌 [لینک دانلود نرم افزارهای استفاده شده در این آموزش: $selected_client->name]($selected_client->link)";


                Telegram::sendVideo([
                    "chat_id" => $sender->id,
                    "video" => InputFile::create(public_path($selected_client->video)),
                    'parse_mode' => 'MarkdownV2',
                    'caption' => $markdownText,
                    'width' => 1280,
                    'height' => 720,

                ]);
                $user->update([
                    'section' => Keyboards::GUIDE,
                    'step' => 3
                ]);
            } else if ($user->step == "1" && $user->section == Keyboards::SUPPORT) {
                SupportMessage::query()->create([
                    'user_id' => $user->id,
                    'message' => $update->getMessage()->text,
                    'status' => "pending"
                ]);
                Telegram::sendMessage([
                    'text' => "✅ پیام شما با موفقیت به ادمین های ربات ارسال شد !",
                    'chat_id' => $sender->id,
                ]);
                $user->update([
                    'section' => Keyboards::SUPPORT,
                    'step' => 2
                ]);
            } else if ($user->step == "1" && $user->section == Keyboards::SERVICES) {
                $user_sub = Subscription::query()->where('user_id', $user->id)->where('slug', $update->getMessage()->text)->first();
                if (is_null($user_sub)) {
                    Telegram::sendMessage([
                        'text' => "⛔️ سرور انتخاب شده نامعتبر می  باشد",
                        'chat_id' => $sender->id,
                    ]);
                } else {
                    $location = $user_sub->service->server->name;
                    $volume = $user_sub->service->package->name;
                    $service_link = $user_sub->service->link;
                    $code = $user_sub->code;
                    $expire_date = $user_sub->expire_at;
                    $message = "💎 *کد سرویس:* `$code`\n" .
                        "🌎 *لوکیشن:* `$location`\n" .
                        "⏳ *تاریخ انقضا:* `$expire_date`\n" .
                        "♾ *حجم کل:* `$volume` \n\n" .
                        "📌 *لینک اشتراک* \n\n" .
                        "`$service_link`";
                    Telegram::sendMessage([
                        'text' => $message,
                        'chat_id' => $sender->id,
                        'parse_mode' => 'MarkdownV2',
                        'reply_markup' => KeyboardHandler::home(),
                    ]);
                }
                return true;
                // $user->update([
                //     'section' => Keyboards::SUPPORT,
                //     'step' => 2
                // ]);
            }
        }

        return "ok";
    }
}
