<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@argus-lan.local'],
            [
                'name' => 'ARGUS Administrator',
                'password' => Hash::make(
                    env('ARGUS_ADMIN_PASSWORD')
                ),
            ]
        );
    }
}
