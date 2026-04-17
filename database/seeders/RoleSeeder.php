<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['Role_Id' => 1, 'Role_Name' => 'Admin'],
            ['Role_Id' => 2, 'Role_Name' => 'Member'],
            ['Role_Id' => 3, 'Role_Name' => 'Choir Leader'],
            ['Role_Id' => 4, 'Role_Name' => 'Finance Officer'],
        ]);
    }
}