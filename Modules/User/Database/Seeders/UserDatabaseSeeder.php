<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Entities\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
        User::query()->create([
            'username' => 'admin',
            'first_name' => 'admin',
            'email' => 'admin@info.com',
            'mobile' => "09010105397",
            'uid' => "1669306764",
            'password' => Hash::make('password'),
            // 'is_superuser' => true
        ]);
    }
}
