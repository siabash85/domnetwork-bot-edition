<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Server\Entities\Subscription;
use Telegram\Bot\Laravel\Facades\Telegram;

class SendExpireServiceNotif extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-expire-service-notif';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {


        $expired_subscriptions = Subscription::query()->where('status', 'active')->whereDate('expire_at', '<=', now())->get();

        foreach ($expired_subscriptions as $key => $epired_subscription) {
            $code = $epired_subscription->code;
            $chat_id = $epired_subscription->user->uid;
            Telegram::sendMessage([
                'text' => "⛔️ سرویس {$code} به علت عدم تمدید حذف شد.",
                "chat_id" => $chat_id,
            ]);
            $epired_subscription->update([
                "status" => "inactive"
            ]);
        }


        $subscriptions = Subscription::query()->where('status', 'active')->whereDate('expire_at', '<=', now()->addDays(3))->get();


        foreach ($subscriptions as $key => $epired_subscription) {
            $code = $epired_subscription->code;
            $chat_id = $epired_subscription->user->uid;
            $message =  "⚠️   کمتر از 3 روز تا انقضای سرویس {$code} باقی مانده است. \n\n" .
                "📌 برای جلوگیری از قطع سرویس، در اسرع وقت نسبت به تمدید سرویس اقدام کنید. \n\n" .
                "⁉️ در صورت منقضی شدن سرویس، حجم باقی مانده سرویس سوخته و سرویس پس از ۳ روز از عدم اقدام به طور کامل حذف خواهد شد.";
            Telegram::sendMessage([
                'text' => $message,
                "chat_id" => $chat_id,
            ]);
        }
    }
}
