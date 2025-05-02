<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            SuburbSeeder::class,
            InstructorSeeder::class,
            AvailabilitySeeder::class,
            ServiceSeeder::class,
        ]);
    }
}