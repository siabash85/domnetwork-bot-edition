<?php

namespace App\Telegram\Keyboard;

use Telegram\Bot\Laravel\Facades\Telegram;

class  KeyboardHandler
{
    public static function home()
    {
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
        return $replyMarkup;
    }
    public static function service()
    {
        $extension_service = ['text' => Keyboards::EXTENSION_SERVICE];
        $home = ['text' => Keyboards::HOME];
        $keyboard = [
            [$extension_service],
            [$home],
        ];
        $replyMarkup = json_encode([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
        ]);
        return $replyMarkup;
    }
}
