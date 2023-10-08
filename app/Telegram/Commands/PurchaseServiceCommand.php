<?php

namespace App\Telegram\Commands;

use Illuminate\Support\Str;
use Modules\User\Entities\User;
use Telegram\Bot\Commands\Command;
use Illuminate\Support\Facades\Hash;

class PurchaseServiceCommand extends Command
{
    protected string $name = 'purchase';
    protected string $description = 'purchase Command to get you started';

    public function handle()
    {
        // $sender = $this->getUpdate()->getMessage()->from;

        $this->replyWithMessage([
            'text' => "purchase service 💸 "
            // 'reply_markup' => $replyMarkup,
        ]);
    }
}
