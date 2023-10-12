<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Server\Entities\PackageDuration;

class PackageDurationTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $durations = [
            ['name' => "7 روز"],
            ['name' => "14 روز"],
            ['name' => "30 روز"],
        ];

        PackageDuration::insert($durations);
    }
}
