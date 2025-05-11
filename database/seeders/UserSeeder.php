<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {


        User::create([
            'name'                       => 'test user',
            'email'                      => 'test@user.com',
            'email_verified_at'          => now(),
            'password'                   => 'password123',
            'profile_photo_path'         => null,
            'two_factor_secret'          => null,
            'two_factor_recovery_codes'  => null,
        ]);


    }
}
