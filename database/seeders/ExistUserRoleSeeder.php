<?php

namespace Database\Seeders;

use App\Helpers\AppHelper;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExistUserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $roles = Role::all();
        foreach($users as $user){
            if($user->role == AppHelper::SUPER_ADMIN){
                $user->role_id = $roles->where('slug', 'admin')->first()->id;
            }elseif($user->role == AppHelper::DEPARTMENT){
                $user->role_id = $roles->where('slug', 'manager')->first()->id;
            }elseif($user->role == AppHelper::STAFF){
                $user->role_id = $roles->where('slug', 'staff')->first()->id;
            }
            $user->save();
        }
    }
}
