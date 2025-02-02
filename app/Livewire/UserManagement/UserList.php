<?php

namespace App\Livewire\UserManagement;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Traits\PermissionsTrait;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class UserList extends Component
{
    use WithPagination, PermissionsTrait;

    public $title = 'List Users';

    // Filter | Sort | Pagination
    #[Url]
    public $search;
    public $showSortingIcon = true;
    public $sortDirection = 'desc';
    public $sortBy = 'users.created_at';
    public $perPage = 10;

    # Filter
    public $isCollapse;
    public $userId;
    public $roleId;
    public $branchId;
    public $departmentId;
    public $statusFilter;
    public $startDateFilter;
    public $endDateFilter;
    public $isDateRangeFilter;

    /**
     * Sort the data by the given column.
     *
     * @param  string  $column
     * @return void
     */
    public function sortColumn($column)
    {
        $this->showSortingIcon = true;
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function getListDataProperty()
    {
        $data = User::selectRaw('users.id,users.name,users.email,users.status,
                users.position,users.role,users.department_id,users.created_at,
                r.name as r_name,dp.name as dp_name,
                bc.name as bc_name'
            )
            ->leftJoin('roles as r', 'users.role_id', 'r.id')
            ->leftJoin('departments as dp', 'users.department_id', 'dp.id')
            ->leftJoin('branches as bc', 'users.branch_id', 'bc.id')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->where(function ($query) {
                return $query->when($this->search, function ($q) {
                    return $q->where('name', 'like', "%$this->search%")
                        ->orWhere('email', 'like', "%$this->search%")
                        ->orWhereRaw("REPLACE(role, '_', ' ') like ?", ["%$this->search%"]);
                });
            })
            ->when((!empty($this->startDateFilter) && !empty($this->endDateFilter)), function($query){
                $query->whereBetween(DB::raw('date(users.created_at)'), [$this->startDateFilter, $this->endDateFilter]);
            })
            ->when(!empty($this->userId), function($query){
                $query->where('users.id', $this->userId);
            })
            ->when(!empty($this->roleId), function($query){
                $query->where('users.role_id', $this->roleId);
            })
            ->when(!empty($this->branchId), function($query){
                $query->where('users.branch_id', $this->branchId);
            })
            ->when(!empty($this->departmentId), function($query){
                $query->where('users.department_id', $this->departmentId);
            })
            ->when($this->statusFilter != NULL, function($query){
                $query->where('users.status', $this->statusFilter);
            })
            ->withoutTjt()
            ->where('users.id', '!=', auth()->id());


        return $data->paginate($this->perPage);
    }

    #[On('delete')]
    public function delete($id)
    {
        try {
            User::find($id)->delete();
            session()->flash('success', 'User successfully deleted.');
            return $this->redirectRoute('user-list', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function changeStatus($id)
    {
        $data = User::findOrFail($id);
        try {
            $data->status = !$data->status;
            $data->save();
            session()->flash('success', 'User status successfully updated.');
            return $this->redirectRoute('user-list', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function collapse()
    {
        $this->isCollapse = empty($this->isCollapse) ? 'show' : NULL;
    }

    public function getDepartmentListProperty()
    {
        return Department::all();
    }

    public function getUserListProperty()
    {
        return User::where('id', '!=', auth()->user()->id)->withoutTjt()->get();
    }

    public function getBranchListProperty()
    {
        return Branch::all();
    }

    public function getPositionListProperty()
    {
        return Role::all();
    }

    #[On('dateRangeFilter')]
    public function dateRangeFilter($startDate, $endDate)
    {
        $this->startDateFilter = $startDate;
        $this->endDateFilter = $endDate;
        $this->isDateRangeFilter = true;
    }

    public function clear()
    {
        return $this->redirectRoute('user-list', navigate: true);
    }

    public function render()
    {
        return view('livewire.user-management.user-list')
            ->title($this->title);
    }
}
