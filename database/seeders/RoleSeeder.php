<?php

namespace Database\Seeders;

use App\Helpers\AppHelper;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::query()->truncate();
        // Create Roles
        $roles = [
            AppHelper::ADMIN => 'Admin',
            AppHelper::DIRECTOR => 'Director',
            AppHelper::MANAGER => 'Manager',
            AppHelper::STAFF => 'Staff',
        ];

        foreach($roles as $slug => $name){
            Role::create(['slug' => $slug, 'name' => $name]);
        }
    }
}
