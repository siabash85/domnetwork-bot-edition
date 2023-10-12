<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Server\Entities\Package;

class PackageTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $packages = [
            ['name' => "30 گیگ", "is_active" => true, "price" => "2000"],
            ['name' => "50 گیگ", "is_active" => true, "price" => "2000"],
            ['name' => "100 گیگ", "is_active" => true, "price" => "2000"],
        ];

        Package::insert($packages);
    }
}
