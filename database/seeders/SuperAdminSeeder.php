<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['phone' => '07742209251'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'role' => 'superadmin',
                'is_active' => true,
            ]
        );
    }
}
