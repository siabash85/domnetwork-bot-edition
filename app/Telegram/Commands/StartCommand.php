<?php

namespace App\Telegram\Commands;

use Illuminate\Support\Str;
use Modules\User\Entities\User;
use Telegram\Bot\Commands\Command;
use Illuminate\Support\Facades\Hash;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Start Command to get you started';

    public function handle()
    {

        $sender = $this->getUpdate()->getMessage()->from;
        $fallbackUsername = $this->getUpdate()->getMessage()->from->username;
        $username = $this->argument(
            'username',
            $fallbackUsername
        );

        $user = User::where('uid', $sender->id)->first();


        if (!$user) {
            $user =    User::query()->create([
                'username' => $sender->username,
                'first_name' =>  $sender->first_name,
                'uid' => $sender->id,
                'password' => Hash::make(Str::random(8)),
            ]);
        }
        $purchase_service = ['text' => '🛒 خرید سرویس'];
        $services = ['text' => '🛍 سرویس های من'];
        $charge = ['text' => '💸 شارژ حساب'];
        $pricing = ['text' => '🛒 تعرفه خدمات'];
        $profile = ['text' => '👤 پروفایل'];
        $support = ['text' => '📮 پشتیبانی آنلاین'];
        $guide = ['text' => '🔗 راهنمای اتصال'];
        $keyboard = [
            [$services, $purchase_service],
            [$charge, $pricing, $profile],
            [$support, $guide],
        ];

        $replyMarkup = json_encode([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);
        $this->replyWithMessage([
            'text' => "سلام {$username} عزیز، به ربات ما خوش آمدید. 🚀\nیکی از دکمه های زیر را انتخاب کنید !",
            'reply_markup' => $replyMarkup,
        ]);
        $user->update([
            'section' => null,
            'step' => null
        ]);
    }
}
