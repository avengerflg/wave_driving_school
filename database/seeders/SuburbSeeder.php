<?php

namespace Database\Seeders;

use App\Models\Suburb;
use Illuminate\Database\Seeder;

class SuburbSeeder extends Seeder
{
    public function run(): void
    {
        $suburbs = [
            ['name' => 'Bondi', 'state' => 'NSW', 'postcode' => '2026', 'active' => true],
            ['name' => 'Surry Hills', 'state' => 'NSW', 'postcode' => '2010', 'active' => true],
            ['name' => 'Parramatta', 'state' => 'NSW', 'postcode' => '2150', 'active' => true],
            ['name' => 'Chatswood', 'state' => 'NSW', 'postcode' => '2067', 'active' => true],
            ['name' => 'Liverpool', 'state' => 'NSW', 'postcode' => '2170', 'active' => true],
            ['name' => 'Manly', 'state' => 'NSW', 'postcode' => '2095', 'active' => true],
            ['name' => 'Newtown', 'state' => 'NSW', 'postcode' => '2042', 'active' => true],
            ['name' => 'Blacktown', 'state' => 'NSW', 'postcode' => '2148', 'active' => true],
        ];

        foreach ($suburbs as $suburb) {
            Suburb::firstOrCreate(
                ['name' => $suburb['name'], 'postcode' => $suburb['postcode']],
                $suburb
            );
        }
    }
}