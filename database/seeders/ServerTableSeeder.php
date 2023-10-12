<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Server\Entities\PackageDuration;
use Modules\Server\Entities\Server;

class ServerTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $servers = [
            ['name' => "🇺🇸 ایالات متحده آمریکا", "stock" => "9999", "is_active" => true, "is_default" => true],
            ['name' => "🇩🇪 آلمان", "stock" => "9999", "is_active" => true, "is_default" => false],
            ['name' => "🇫🇮 فنلاند", "stock" => "9999", "is_active" => true, "is_default" => false],
        ];

        Server::insert($servers);
    }
}
