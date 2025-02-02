<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(DepartmentSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(ModuleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RolePermissionSeeder::class);
        // $this->call(ExistUserRoleSeeder::class);
        $this->call(SalesLeadModelSeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(UserSeeder::class);
    }
}
