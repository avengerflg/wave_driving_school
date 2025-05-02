<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Suburb;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class InstructorSeeder extends Seeder
{
    public function run(): void
    {
        // Get all suburbs to assign to instructors
        $suburbs = Suburb::where('active', true)->pluck('id')->toArray();
        
        // Make sure we have at least one suburb
        if (empty($suburbs)) {
            $this->command->error('No active suburbs found! Make sure to run SuburbSeeder first.');
            return;
        }

        // Find Chatswood's ID to ensure we include it
        $chatswoodId = Suburb::where('name', 'Chatswood')->value('id');
        
        // Create instructors with their user accounts
        $instructors = [
            [
                'name' => 'John Smith',
                'email' => 'john@wavedrivingschool.com',
                'password' => 'password123',
                'license_number' => 'DI123456',
                'bio' => 'Experienced instructor with over 10 years of teaching. Specializes in helping nervous drivers gain confidence.',
                'active' => true,
                'suburbs' => $chatswoodId ? array_merge(array_slice($suburbs, 0, 3), [$chatswoodId]) : array_slice($suburbs, 0, 4)
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@wavedrivingschool.com',
                'password' => 'password123',
                'license_number' => 'DI654321',
                'bio' => 'Patient instructor who focuses on defensive driving techniques and road safety.',
                'active' => true,
                'suburbs' => $chatswoodId ? array_merge(array_slice($suburbs, 2, 3), [$chatswoodId]) : array_slice($suburbs, 2, 4)
            ],
            [
                'name' => 'Michael Wong',
                'email' => 'michael@wavedrivingschool.com',
                'password' => 'password123',
                'license_number' => 'DI789012',
                'bio' => 'Fluent in English, Mandarin and Cantonese. Specializes in helping international students.',
                'active' => true,
                'suburbs' => array_slice($suburbs, 4, 4)
            ],
            [
                'name' => 'Amanda Lee',
                'email' => 'amanda@wavedrivingschool.com',
                'password' => 'password123',
                'license_number' => 'DI345678',
                'bio' => 'Calm and methodical instructor with expertise in parallel parking and reverse parking.',
                'active' => true,
                'suburbs' => array_slice($suburbs, 1, 3)
            ],
        ];
        
        foreach ($instructors as $instructorData) {
            // Check if user already exists
            $user = User::firstOrNew(['email' => $instructorData['email']]);
            
            if (!$user->exists) {
                // Only set these attributes if the user is new
                $user->name = $instructorData['name'];
                $user->password = Hash::make($instructorData['password']);
                $user->save();
                
                // Assign instructor role
                $user->assignRole('instructor');
            }
            
            // Check if instructor already exists
            $instructor = Instructor::firstOrNew(['user_id' => $user->id]);
            
            if (!$instructor->exists) {
                $instructor->license_number = $instructorData['license_number'];
                $instructor->bio = $instructorData['bio'];
                $instructor->active = $instructorData['active'];
                $instructor->suburbs = $instructorData['suburbs'];
                $instructor->save();
                
                // Comment out the attachment until we have the pivot table
                // if (Schema::hasTable('instructor_suburb')) {
                //     $instructor->suburbs()->attach($instructorData['suburbs']);
                // }
            }
        }
        
        $this->command->info('Instructors seeded successfully!');
    }
}