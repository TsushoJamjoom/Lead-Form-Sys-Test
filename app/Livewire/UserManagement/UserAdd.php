<?php

namespace App\Livewire\UserManagement;

use App\Helpers\AppHelper;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;

class UserAdd extends Component
{
    public $name;
    public $email;
    public $password;
    public $confirm_password;
    public $role_id;
    public $department_id;
    public $status;
    public $branch_id;

    public $title = 'Add User';

    public function getDepartmentListProperty()
    {
        return Department::all();
    }

    public function getBranchListProperty()
    {
        return Branch::all();
    }

    public function getPositionListProperty()
    {
        return Role::all();
    }

    public function store()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:20',
            'email' => 'required|email:rfx,dns|max:30|unique:users,email',
            'password' => 'required|min:3|max:10',
            'confirm_password' => 'required_with:password|same:password|min:3',
            'department_id' => 'required',
            'branch_id' => 'required',
            'role_id' => 'required',
            'status' => 'required|numeric',
        ]);

        try {
            User::create($validated);
            session()->flash('success', 'User successfully created.');
            return $this->redirectRoute('user-list', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user-management.user-add')
            ->title($this->title);
    }
}
