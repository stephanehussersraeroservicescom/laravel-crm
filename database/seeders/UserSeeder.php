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
                'name' => 'stef',
                'password' => Hash::make('ste67676767'),
                'role' => 'sales',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'dom@jmail.con'],
            [
                'name' => 'dominic',
                'password' => Hash::make('abc123456'),
                'role' => 'sales',
                'email_verified_at' => now(),
            ]
        );
    }
}
