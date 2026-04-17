<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transaction_categories')->insert([
            ['Category_Name' => 'Tithes'],
            ['Category_Name' => 'Offering'],
            ['Category_Name' => 'Utilities'],
            ['Category_Name' => 'Donation'],
        ]);
    }
}
