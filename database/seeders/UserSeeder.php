<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // ✅ Create Admin Account
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'), // Change for security
            'role' => 'admin',
            'company_id' => '1',
            'is_first_login' => false
        ]);

        // ✅ Create Doctor Account
        User::create([
            'name' => 'Doctor John',
            'email' => 'doctor@example.com',
            'password' => Hash::make('password123'), // Change for security
            'role' => 'doctor',
            'company_id' => '1',
            'is_first_login' => false
        ]);
    }
}
