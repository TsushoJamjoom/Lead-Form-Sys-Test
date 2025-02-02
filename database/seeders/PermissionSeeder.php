<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->truncate();
        $modules = Module::all();

        $permissions = [
            'create' => 'Create',
            'edit' => 'Edit',
            'view' => 'View',
            'list' => 'List',
            'delete' => 'Delete',
        ];

        foreach($modules as $module){
            foreach($permissions as $slug => $name){
                Permission::create([
                    'module_id' => $module->id, 'slug' => $module->slug .'/' . $slug, 'name' => $module->name .' ' . $name
                ]);
            }
        }

        $salesLeadModule = $modules->where('slug', 'sales_lead')->first();
        // Sales Lead Revert Lost/Achieved
        Permission::create([
            'module_id' => $salesLeadModule->id, 'slug' => $salesLeadModule->slug .'/revert', 'name' => $salesLeadModule->name .' Revert'
        ]);
        $customerModule = $modules->where('slug', 'customer')->first();
        // Customer Export/Import
        Permission::create([
            'module_id' => $customerModule->id, 'slug' => $customerModule->slug .'/export', 'name' => $customerModule->name .' Export'
        ]);
        Permission::create([
            'module_id' => $customerModule->id, 'slug' => $customerModule->slug .'/import', 'name' => $customerModule->name .' Import'
        ]);
    }
}
