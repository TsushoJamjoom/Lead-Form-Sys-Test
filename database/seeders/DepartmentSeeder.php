<?php

namespace Database\Seeders;

use App\Helpers\AppHelper;
use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::updateOrCreate(['slug' => AppHelper::SALES],[
            'slug' => AppHelper::SALES,
            'name' => 'Sales'
        ]);

        Department::updateOrCreate(['slug' => AppHelper::SPARE_PARTS],[
            'slug' => AppHelper::SPARE_PARTS,
            'name' => 'Spare Parts'
        ]);

        Department::updateOrCreate(['slug' => AppHelper::SERVICE],[
            'slug' => AppHelper::SERVICE,
            'name' => 'Service'
        ]);

        Department::updateOrCreate(['slug' => AppHelper::AFTER_SALES],[
            'slug' => AppHelper::AFTER_SALES,
            'name' => 'After Sales (Spare Parts & Service)'
        ]);

        Department::updateOrCreate(['slug' => AppHelper::S3],[
            'slug' => AppHelper::S3,
            'name' => '3S'
        ]);
    }
}
