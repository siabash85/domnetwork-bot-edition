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
            'password' => Hash::make('admin4030@A'),
            // 'is_superuser' => true
        ]);

        User::query()->create([
            'username' => 'dev',
            'first_name' => 'test',
            'email' => 'test@info.com',
            'mobile' => "09224729521",
            'uid' => "1669306762",
            'password' => Hash::make('password'),
            // 'is_superuser' => true
        ]);
    }
}
