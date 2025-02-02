<?php

namespace Database\Seeders;

use App\Helpers\AppHelper;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::query()->truncate();
        Branch::create(['slug' => 'riyadh', 'name' => AppHelper::RIYADH]);
        Branch::create(['slug' => 'jeddah', 'name' => AppHelper::JEDDAH]);
        Branch::create(['slug' => 'dammam', 'name' => AppHelper::DAMMAM]);
        Branch::create(['slug' => 'khamis_mushait', 'name' => AppHelper::KHAMIS_MUSHAIT]);
        Branch::create(['slug' => 'ksa', 'name' => AppHelper::KSA]);
    }
}
