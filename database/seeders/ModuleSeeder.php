<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('modules')->truncate();
        $modules = [
            'customer' => 'Customer',
            'user' => 'User',
            'ticket' => 'Ticket',
            'calendar' => 'Calendar',
            'history' => 'History',
            'sales_lead' => 'Sales Lead',
            'map' => 'Map',
        ];

        foreach($modules as $slug => $name){
            Module::create(['slug' => $slug, 'name' => $name]);
        }
    }
}
