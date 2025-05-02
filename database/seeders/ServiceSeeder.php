<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            [
                'name' => 'Standard Driving Lesson',
                'description' => '60-minute standard driving lesson for beginners to intermediate learners.',
                'price' => 65.00,
                'duration' => 60, // minutes
                'active' => true,
            ],
            [
                'name' => 'Extended Driving Lesson',
                'description' => '90-minute comprehensive driving lesson with advanced techniques.',
                'price' => 95.00,
                'duration' => 90,
                'active' => true,
            ],
            [
                'name' => 'Test Preparation Package',
                'description' => '120-minute lesson focusing on test preparation and common test routes.',
                'price' => 120.00,
                'duration' => 120,
                'active' => true,
            ],
            [
                'name' => 'Refresher Course',
                'description' => '45-minute lesson for licensed drivers who want to improve their skills.',
                'price' => 50.00,
                'duration' => 45,
                'active' => true,
            ],
            [
                'name' => 'Defensive Driving Course',
                'description' => '120-minute specialized lesson focusing on defensive driving techniques.',
                'price' => 130.00,
                'duration' => 120,
                'active' => true,
            ],
            [
                'name' => 'Night Driving Lesson',
                'description' => '90-minute lesson specifically for night driving practice.',
                'price' => 100.00,
                'duration' => 90,
                'active' => true,
            ],
            [
                'name' => 'Highway Confidence Lesson',
                'description' => '75-minute lesson focused on highway and freeway driving.',
                'price' => 85.00,
                'duration' => 75,
                'active' => true,
            ],
            [
                'name' => 'Parking Skills Package',
                'description' => '60-minute lesson focusing on various parking techniques.',
                'price' => 70.00,
                'duration' => 60,
                'active' => true,
            ],
            [
                'name' => 'International Driver Adaptation',
                'description' => '120-minute lesson for international drivers adapting to local rules.',
                'price' => 125.00,
                'duration' => 120,
                'active' => true,
            ],
            [
                'name' => 'Senior Refresher Course',
                'description' => '60-minute lesson designed specifically for senior drivers.',
                'price' => 75.00,
                'duration' => 60,
                'active' => true,
            ],
            // Some inactive services for testing
            [
                'name' => 'Discontinued Package',
                'description' => 'This package is no longer available.',
                'price' => 55.00,
                'duration' => 45,
                'active' => false,
            ],
            [
                'name' => 'Seasonal Winter Driving',
                'description' => 'Special winter driving course (seasonal).',
                'price' => 110.00,
                'duration' => 90,
                'active' => false,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
