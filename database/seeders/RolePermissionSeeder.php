<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('role_permissions')->truncate();

        $roles = Role::all();
        $modules = Module::all();
        $permissions = Permission::all();

        foreach ($roles as $role) {
            // Admin and Directory Assign Same Permission
            if ($role->slug == 'admin') {
                $role->permissions()->sync($permissions->pluck('id'));
            } elseif ($role->slug == 'director') {
                $role->permissions()->sync($permissions->pluck('id'));
            } elseif ($role->slug == 'manager') {
                $userModuleId = $modules->where('slug', '=', 'user')->first()->id;
                $customerModuleId = $modules->where('slug', '=', 'customer')->first()->id;
                $excludedPermissions = $permissions->where('module_id', '!=', $userModuleId)
                    ->whereNotIn('slug', ['customer/delete', 'ticket/delete', 'customer/export', 'customer/import']);
                $role->permissions()->sync($excludedPermissions->pluck('id'));
            } elseif ($role->slug == 'staff') {
                // Customer Module
                $customerModuleId = $modules->where('slug', '=', 'customer')->first()->id;
                $excludedPermissions = $permissions->whereIn('slug', ['customer/list', 'customer/view', 'customer/edit'])
                    ->where('module_id', '=', $customerModuleId);
                $ids = $excludedPermissions->pluck('id')->toArray();

                // History & Calendar Modules
                $moduleIds = $modules->whereIn('slug', ['history', 'calendar', 'map', 'sales_lead'])->pluck('id');
                $includePermissions = $permissions->whereIn('module_id', $moduleIds)->whereNotIn('slug', ['sales_lead/delete', 'sales_lead/edit']);;
                // ->whereNotIn('slug', ['sales_lead/revert']);
                $ids = array_merge($includePermissions->pluck('id')->toArray(), $ids);

                // Ticket Module
                $ticketModuleId = $modules->where('slug', '=', 'ticket')->first()->id;
                $excludedPermissions = $permissions->whereIn('slug', ['ticket/create', 'ticket/view', 'ticket/list'])
                    ->where('module_id', '=', $ticketModuleId);
                $ids = array_merge($excludedPermissions->pluck('id')->toArray(), $ids);

                $role->permissions()->sync($ids);
            }
        }
    }
}
