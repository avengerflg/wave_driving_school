<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Check if admin user already exists
        $existingAdmin = User::where('email', 'admin@wavedrivingschool.com')->first();
        
        if (!$existingAdmin) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@wavedrivingschool.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            
            $admin->assignRole('admin');
            $this->command->info('Admin user created successfully.');
        } else {
            // If admin already exists, make sure they have the admin role
            $existingAdmin->assignRole('admin');
            $this->command->info('Admin user already exists. Role verified.');
        }
    }
}

