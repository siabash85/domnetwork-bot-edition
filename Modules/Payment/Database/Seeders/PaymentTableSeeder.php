<?php

namespace Modules\Payment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Payment\Entities\PaymentMethod;

class PaymentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $methods = [
            [
                "title" => "درگاه پرداخت",
                "description" => "پرداخت اینترنتی",
                "type" => "online",
                "status" => "inactive",
                "is_default" => true,
            ],
            [
                "title" => "کارت به کارت",
                "description" => "پرداخت با کارت بانکی",
                "type" => "card",
                "status" => "active",
                "is_default" => false,
            ],
            [
                "title" => "پرداخت ارزی",
                "description" => "پرداخت با ارز دیجیتال",
                "type" => "crypto",
                "status" => "inactive",
                "is_default" => false,
            ],
            [
                "title" => "🎁 ووچر",
                "description" => "پرداخت با  ووچر",
                "type" => "voucher",
                "status" => "active",
                "is_default" => false,
            ],

        ];

        PaymentMethod::query()->insert($methods);
    }
}
