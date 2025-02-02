<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\AppHelper;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->truncate();

        $roles = Role::all();
        $departments = Department::all();
        $branches = Branch::all();

        foreach($roles as $role){
            if($role->slug == AppHelper::ADMIN){
                $departmentS3 = $departments->where('slug', AppHelper::S3)->first();
                $branchJeddah = $branches->where('name', AppHelper::JEDDAH)->first();

                User::create([
                    'name' => 'Nada Rambu',
                    'email' => 'nada_rambu@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentS3->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
            }elseif($role->slug == AppHelper::DIRECTOR){
                $departmentSales = $departments->where('slug', AppHelper::SALES)->first();
                $departmentAfterSales = $departments->where('slug', AppHelper::AFTER_SALES)->first();
                $branchJeddah = $branches->where('name', AppHelper::JEDDAH)->first();

                User::create([
                    'name' => 'Hiroki Ozeki',
                    'email' => 'hiroki_ozeki@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Islam Abougraida',
                    'email' => 'islam_abougrida@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Faisal Khan',
                    'email' => 'faisal_khan@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentAfterSales->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
            }elseif($role->slug == AppHelper::MANAGER){
                $departmentSales = $departments->where('slug', AppHelper::SALES)->first();
                $departmentAfterSales = $departments->where('slug', AppHelper::AFTER_SALES)->first();
                $departmentService = $departments->where('slug', AppHelper::SERVICE)->first();
                $branchJeddah = $branches->where('name', AppHelper::JEDDAH)->first();
                $branchRiyadh = $branches->where('name', AppHelper::RIYADH)->first();

                User::create([
                    'name' => 'Mohammed Aldalti',
                    'email' => 'mohammed_aldalati@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Sayed Khalid',
                    'email' => 'sayed_khalid@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Hussain Assi',
                    'email' => 'hussain_assi@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Hisham Abdukhakim',
                    'email' => 'hisham_abdulhakim@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchRiyadh->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Yu Arisaka',
                    'email' => 'yu_arisaka@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentAfterSales->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Hamza Zayed',
                    'email' => 'hamza_zayed@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentService->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);

            }elseif($role->slug == AppHelper::STAFF){
                $departmentSales = $departments->where('slug', AppHelper::SALES)->first();
                $departmentAfterSales = $departments->where('slug', AppHelper::AFTER_SALES)->first();
                $departmentService = $departments->where('slug', AppHelper::SERVICE)->first();
                $departmentSparePart = $departments->where('slug', AppHelper::SPARE_PARTS)->first();
                $department3S = $departments->where('slug', AppHelper::S3)->first();
                $branchJeddah = $branches->where('name', AppHelper::JEDDAH)->first();
                $branchRiyadh = $branches->where('name', AppHelper::RIYADH)->first();

                User::create([
                    'name' => 'Hussain Alguthmi',
                    'email' => 'hussain_alguthmi@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Mahmoud Azmi',
                    'email' => 'mahmoud_azmi@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Mohammed Shelwan',
                    'email' => 'mohammed_shelwan@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Ryotaro Hanaki',
                    'email' => 'ryotaro_hanaki@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Mohammed Sultan',
                    'email' => 'mohamad_sultan@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchRiyadh->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Haitham Elsherif',
                    'email' => 'haitham_elsherif@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchRiyadh->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Ahmed Saad',
                    'email' => 'ahmed_saad@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchRiyadh->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Nourah Abdullah',
                    'email' => 'nourah_abdullah@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSales->id,
                    'branch_id' => $branchRiyadh->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Saadeldein Ibrahim',
                    'email' => 'saadeldein_ibrahim@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentAfterSales->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Hossam Rajab',
                    'email' => 'hossam_rajab@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentService->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Asim Koshak',
                    'email' => 'asim_koshak@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSparePart->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Alaa Bahmdan',
                    'email' => 'alaa_bahmdan@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSparePart->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Ghazwan Zatari',
                    'email' => 'ghazwan_zatari@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $departmentSparePart->id,
                    'branch_id' => $branchRiyadh->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Sultan Alzahrani',
                    'email' => 'sultan_alzahrani@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $department3S->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Naved Khan',
                    'email' => 'naved_khan@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $department3S->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
                User::create([
                    'name' => 'Bilal Mohammed',
                    'email' => 'bilal_mohammed@tjt.com.sa',
                    'password' => bcrypt('Test@123'),
                    'role_id' => $role->id,
                    'department_id' => $department3S->id,
                    'branch_id' => $branchJeddah->id,
                    'status' => 1
                ]);
            }
        }
    }
}
