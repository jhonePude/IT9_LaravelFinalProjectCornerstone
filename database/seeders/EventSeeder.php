<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('events')->insert([
            [
                'Title' => 'Sunday Morning Service',
                'Description' => 'Experience a powerful time of praise and worship as a community.',
                'Event_Date' => '2025-08-24',
                'Event_Time' => '09:00:00',
                'Location' => 'Main Sanctuary',
                'Image_Banner' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=600&q=80',
                'Event_Category_Id' => 1 // Worship/Service
            ],
            [
                'Title' => 'Infant Christening Ceremony',
                'Description' => 'A sacred ritual of the church reflecting our faith and commitment.',
                'Event_Date' => '2025-08-24',
                'Event_Time' => '11:00:00',
                'Location' => 'Baptismal Area',
                'Image_Banner' => 'https://png.pngtree.com/thumb_back/fh260/background/20230308/pngtree-accessories-for-the-baptism-of-a-child-religion-christening-male-photo-image_50079514.jpg',
                'Event_Category_Id' => 2 // Sacrament
            ]
        ]);
    }
}