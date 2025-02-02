<?php

namespace App\Livewire\UserManagement;

use App\Helpers\AppHelper;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;

class UserEdit extends Component
{
    public $id;
    public $name;
    public $email;
    public $password;
    public $role_id;
    public $department_id;
    public $status;
    public $branch_id;

    public $title = "Edit User";

    public function mount($id)
    {
        $data = User::findOrFail($id);
        $this->id = $data->id;
        $this->name = $data->name;
        $this->email = $data->email;
        $this->role_id = $data->role_id;
        $this->department_id = $data->department_id;
        $this->status = $data->status;
        $this->branch_id = $data->branch_id;
    }

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

    public function update()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:20',
            'email' => 'required|email:rfx,dns|max:30|unique:users,email,' . $this->id,
            'role_id' => 'required',
            'department_id' => 'required',
            'status' => 'required|numeric',
            'branch_id' => 'required',
        ]);

        try {
            User::where('id', $this->id)->update($validated);
            session()->flash('success', 'User successfully created.');
            return $this->redirectRoute('user-list', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user-management.user-edit')
        ->title($this->title);
    }
}
