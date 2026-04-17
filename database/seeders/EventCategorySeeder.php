<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('event_categories')->insert([
            ['Event_Category_Name' => 'Worship'],
            ['Event_Category_Name' => 'Sacrament'],
            ['Event_Category_Name' => 'Youth'],
            ['Event_Category_Name' => 'Community'],
            ['Event_Category_Name' => 'Practice'],
        ]);
    }
}