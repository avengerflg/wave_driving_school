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
            // QLD Suburbs
            ['name' => 'Albion', 'state' => 'QLD', 'postcode' => '4010', 'active' => true],
            ['name' => 'Alderley', 'state' => 'QLD', 'postcode' => '4051', 'active' => true],
            ['name' => 'Arana Hills', 'state' => 'QLD', 'postcode' => '4054', 'active' => true],
            ['name' => 'Ascot', 'state' => 'QLD', 'postcode' => '4007', 'active' => true],
            ['name' => 'Aspley', 'state' => 'QLD', 'postcode' => '4034', 'active' => true],
            ['name' => 'Bald Hills', 'state' => 'QLD', 'postcode' => '4036', 'active' => true],
            ['name' => 'Banyo', 'state' => 'QLD', 'postcode' => '4014', 'active' => true],
            ['name' => 'Boondall', 'state' => 'QLD', 'postcode' => '4034', 'active' => true],
            ['name' => 'Bracken Ridge', 'state' => 'QLD', 'postcode' => '4017', 'active' => true],
            ['name' => 'Bray Park', 'state' => 'QLD', 'postcode' => '4500', 'active' => true],
            ['name' => 'Bridgeman Downs', 'state' => 'QLD', 'postcode' => '4035', 'active' => true],
            ['name' => 'Brighton', 'state' => 'QLD', 'postcode' => '4017', 'active' => true],
            ['name' => 'Brisbane Airport', 'state' => 'QLD', 'postcode' => '4008', 'active' => true],
            ['name' => 'Carseldine', 'state' => 'QLD', 'postcode' => '4034', 'active' => true],
            ['name' => 'Chermside', 'state' => 'QLD', 'postcode' => '4032', 'active' => true],
            ['name' => 'Chermside West', 'state' => 'QLD', 'postcode' => '4032', 'active' => true],
            ['name' => 'Deagon', 'state' => 'QLD', 'postcode' => '4017', 'active' => true],
            ['name' => 'Enoggera', 'state' => 'QLD', 'postcode' => '4051', 'active' => true],
            ['name' => 'Everton Park', 'state' => 'QLD', 'postcode' => '4053', 'active' => true],
            ['name' => 'Fitzgibbon', 'state' => 'QLD', 'postcode' => '4018', 'active' => true],
            ['name' => 'Gaythorne', 'state' => 'QLD', 'postcode' => '4051', 'active' => true],
            ['name' => 'Geebung', 'state' => 'QLD', 'postcode' => '4034', 'active' => true],
            ['name' => 'Gordon Park', 'state' => 'QLD', 'postcode' => '4031', 'active' => true],
            ['name' => 'Grange', 'state' => 'QLD', 'postcode' => '4051', 'active' => true],
            ['name' => 'Hamilton', 'state' => 'QLD', 'postcode' => '4007', 'active' => true],
            ['name' => 'Hendra', 'state' => 'QLD', 'postcode' => '4011', 'active' => true],
            ['name' => 'Kalinga', 'state' => 'QLD', 'postcode' => '4030', 'active' => true],
            ['name' => 'Kedron', 'state' => 'QLD', 'postcode' => '4031', 'active' => true],
            ['name' => 'Lawnton', 'state' => 'QLD', 'postcode' => '4501', 'active' => true],
            ['name' => 'Lutwyche', 'state' => 'QLD', 'postcode' => '4030', 'active' => true],
            ['name' => 'McDowall', 'state' => 'QLD', 'postcode' => '4053', 'active' => true],
            ['name' => 'Mitchelton', 'state' => 'QLD', 'postcode' => '4053', 'active' => true],
            ['name' => 'Newmarket', 'state' => 'QLD', 'postcode' => '4051', 'active' => true],
            ['name' => 'Northgate', 'state' => 'QLD', 'postcode' => '4013', 'active' => true],
            ['name' => 'Nudgee', 'state' => 'QLD', 'postcode' => '4014', 'active' => true],
            ['name' => 'Nudgee Beach', 'state' => 'QLD', 'postcode' => '4014', 'active' => true],
            ['name' => 'Nundah', 'state' => 'QLD', 'postcode' => '4012', 'active' => true],
            ['name' => 'Sandgate', 'state' => 'QLD', 'postcode' => '4017', 'active' => true],
            ['name' => 'Shorncliffe', 'state' => 'QLD', 'postcode' => '4017', 'active' => true],
            ['name' => 'Stafford', 'state' => 'QLD', 'postcode' => '4053', 'active' => true],
            ['name' => 'Stafford Heights', 'state' => 'QLD', 'postcode' => '4053', 'active' => true],
            ['name' => 'Taigum', 'state' => 'QLD', 'postcode' => '4018', 'active' => true],
            ['name' => 'Virginia', 'state' => 'QLD', 'postcode' => '4014', 'active' => true],
            ['name' => 'Wavell Heights', 'state' => 'QLD', 'postcode' => '4012', 'active' => true],
            ['name' => 'Wilston', 'state' => 'QLD', 'postcode' => '4051', 'active' => true],
            ['name' => 'Windsor', 'state' => 'QLD', 'postcode' => '4030', 'active' => true],
            ['name' => 'Wooloowin', 'state' => 'QLD', 'postcode' => '4030', 'active' => true],
            ['name' => 'Zillmere', 'state' => 'QLD', 'postcode' => '4034', 'active' => true],
        ];

        foreach ($suburbs as $suburb) {
            Suburb::firstOrCreate(
                ['name' => $suburb['name'], 'postcode' => $suburb['postcode']],
                $suburb
            );
        }
    }
}