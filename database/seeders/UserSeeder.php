<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'stef@jmail.con'],
            [
                'name' => 'Ste Admin',
                'password' => Hash::make('ste67676767'),
                'email_verified_at' => now(),
            ]
        );
    }
}
