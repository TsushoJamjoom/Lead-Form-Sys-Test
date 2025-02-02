<?php

namespace App\Livewire\History;

use App\Helpers\AppHelper;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\History;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class HistoryList extends Component
{
    use WithPagination;

    public $user;

    public $title = 'History';

    // Filter | Sort | Pagination
    public $search;
    public $showSortingIcon = true;
    public $sortDirection = 'desc';
    public $sortBy = 'created_at';
    public $perPage = 10;
    public $searchId;
    public $branchId;

    # Filter
    public $isCollapse;
    public $startDateFilter;
    public $endDateFilter;
    public $isDateRangeFilter;
    public $salesUserId;

    public function mount()
    {
        $this->user = auth()->user();
    }

    public function getCompanyDropDownProperty()
    {
        return Company::createdMe()
            ->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                return $query->orWhere('sales_user_id', $this->user->id)
                    ->orWhere('sales_user_id', 0);
            })
            ->when(AppHelper::isSalesDeptUser($this->user), function ($query) {
                return $query->where(function ($q) {
                    $q->whereHas('salesUser', function ($q) {
                        return $q->where('role', '!=', 'TJT');
                    })->orWhere('sales_user_id', 0);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getSalesUsersProperty()
    {
        return AppHelper::getSalesAnd3sUser();
    }

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
        $data = History::with(['updatedBy', 'salesUser', 'branch'])
            ->createdMe()
            ->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                return $query->whereIn('sales_user_id', [(int)$this->user->id, 0]);
            })
            ->when(AppHelper::isSalesDeptUser($this->user), function ($query) {
                return $query->where(function ($q) {
                    $q->whereHas('salesUser', function ($q) {
                        return $q->where('role', '!=', 'TJT');
                    })->orWhere('sales_user_id', 0);
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->where(function ($query) {
                return $query->when($this->search, function ($q) {
                    return $q->where('company_name', 'like', "%$this->search%")
                        ->orWhere('contact_person', 'like', "%$this->search%")
                        ->orWhere('customer_code', 'like', "%$this->search%")
                        ->orWhere('email', 'like', "%$this->search%")
                        ->orWhere('mobile_no', 'like', "%$this->search%");
                });
            })
            ->when($this->searchId, function ($query) {
                return $query->where('company_id', $this->searchId);
            })
            ->when((!empty($this->startDateFilter) && !empty($this->endDateFilter)), function ($query) {
                $query->whereBetween(DB::raw('date(created_at)'), [$this->startDateFilter, $this->endDateFilter]);
            })
            ->when($this->salesUserId != '', function ($query) {
                return $query->where('sales_user_id', $this->salesUserId);
            })
            ->when($this->branchId, function ($query) {
                return $query->where('branch_id', $this->branchId);
            });

        return $data->paginate($this->perPage);
    }

    public function collapse()
    {
        $this->isCollapse = empty($this->isCollapse) ? 'show' : NULL;
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
        return $this->redirectRoute('history-list', navigate: true);
    }

    public function getBranchListProperty()
    {
        return Branch::all();
    }

    public function render()
    {
        return view('livewire.history.history-list');
    }
}
