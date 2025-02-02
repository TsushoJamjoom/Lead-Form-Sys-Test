<?php

namespace Database\Seeders;

use App\Helpers\AppHelper;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TjtUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleId = Role::where('slug', AppHelper::STAFF)->first()->id;
        $departmentId = Department::where('slug', AppHelper::SALES)->first()->id;
        $branchId = Branch::where('slug', AppHelper::KSA)->first()->id;
        User::create([
            'role_id' => $roleId,
            'name' => 'TJT',
            'email' => 'tjt@mailinator.com',
            'role' => 'TJT',
            'status' => 1,
            'password' => bcrypt('Test@123'),
            'department_id' => $departmentId,
            'branch_id' => $branchId,
        ]);
    }
}
