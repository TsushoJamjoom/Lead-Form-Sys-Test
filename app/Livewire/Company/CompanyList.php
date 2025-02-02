<?php

namespace App\Livewire\Company;

use App\Exports\CompanyExport;
use App\Helpers\AppHelper;
use App\Imports\CompanyExistCheck;
use App\Imports\CompanyImport;
use App\Models\Branch;
use App\Models\Company;
use App\Models\CustomerEvent;
use App\Models\Department;
use App\Models\History;
use App\Models\Role;
use App\Models\SalesLead;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class CompanyList extends Component
{
    use WithPagination, WithFileUploads;

    public $title = 'List Company';

    // Filter | Sort | Pagination
    public $search;
    public $showSortingIcon = true;
    public $sortDirection = 'desc';
    public $sortBy = 'created_at';
    public $perPage = 10;
    public $searchId;

    public $importFile;
    public $user;

    # Filter
    public $isCollapse;
    public $startDateFilter;
    public $endDateFilter;
    public $isDateRangeFilter;
    public $salesUserId;
    public $branchId;

    public function mount()
    {
        $this->user = auth()->user();
        $this->startDateFilter = Carbon::now()->startOfYear();
        $this->endDateFilter = Carbon::now();
    }

    public function getStartDateLabelProperty()
    {
        return Carbon::now()->startOfYear()->format('d/m/Y');
    }

    public function getEndDateLabelProperty()
    {
        return Carbon::now()->format('d/m/Y');
    }

    public function updatedImportFile($value)
    {
        try {
            // Check Existing Customer
            $import = new CompanyExistCheck;
            Excel::import($import, $value);
            if (count($import->getExistingCustomers()) > 0) {
                $this->dispatch('import-confirm');
                return false;
            }
            // dd(Excel::toArray(new CompanyImport, $value));
            Excel::import(new CompanyImport, $value);
            session()->flash('success', 'Record successfully imported.');
            return $this->redirectRoute('company-list', navigate: true);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' Line:' . $e->getLine() . ' File:' . $e->getFile());
            $this->dispatch('alert', type: 'error', message: "Internal Server Error.");
        }
    }

    #[On('import-overwrite')]
    public function importOverWrite()
    {
        Excel::import(new CompanyImport, $this->importFile);
        session()->flash('success', 'Record successfully imported.');
        return $this->redirectRoute('company-list', navigate: true);
    }

    public function exportFile()
    {
        $name = date('d-m-Y-H-i-s') . '-' . 'company.xlsx';
        return Excel::download(new CompanyExport, $name);
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

    public function getCompanyDropDownProperty()
    {
        $data = Company::createdMe()
            ->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                return $query->whereIn('sales_user_id', [$this->user->id, 0]);
            })
            ->when(AppHelper::isSalesDeptUser($this->user), function ($query) {
                return $query->where(function ($q) {
                    $q->whereHas('salesUser', function ($q) {
                        return $q->where('role', '!=', 'TJT');
                    })->orWhere('sales_user_id', 0);
                });
            })
            ->orderBy('created_at', 'desc');

        return $data->get();
    }

    public function getSalesUsersProperty()
    {
        return AppHelper::getSalesAnd3sUser();
    }

    public function getListDataProperty()
    {
        DB::enableQueryLog();
        $data = Company::with(['salesUser', 'branch'])->createdMe()->orderBy($this->sortBy, $this->sortDirection)
            ->where(function ($query) {
                return $query->when($this->search, function ($q) {
                    return $q->where('company_name', 'like', "%$this->search%")
                        ->orWhere('contact_person', 'like', "%$this->search%")
                        ->orWhere('customer_code', 'like', "%$this->search%")
                        ->orWhere('email', 'like', "%$this->search%")
                        ->orWhere('mobile_no', 'like', "%$this->search%");
                });
            })
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
            ->when((!empty($this->startDateFilter) && !empty($this->endDateFilter)), function ($query) {
                $query->whereBetween(DB::raw('date(created_at)'), [$this->startDateFilter, $this->endDateFilter]);
            })
            ->when($this->salesUserId != '', function ($query) {
                return $query->where('sales_user_id', $this->salesUserId);
            })
            ->when($this->searchId != '', function ($query) {
                return $query->where('id', (int)$this->searchId);
            })
            ->when($this->branchId, function ($query) {
                return $query->where('branch_id', $this->branchId);
            });

        // dd(DB::getQueryLog());

        return $data->paginate($this->perPage);
    }

    #[On('delete')]
    public function delete($id)
    {
        try {
            if (Company::createdMe()->find($id)->delete()) {
                Ticket::where('company_id', $id)->delete();
                SalesLead::where('company_id', $id)->delete();
                History::where('company_id', $id)->delete();
                CustomerEvent::where('company_id', $id)->delete();
            }
            session()->flash('success', 'Record successfully deleted.');
            return $this->redirectRoute('company-list', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
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
        return $this->redirectRoute('company-list', navigate: true);
    }

    public function getBranchListProperty()
    {
        return Branch::all();
    }

    public function render()
    {
        return view('livewire.company.company-list')
            ->title($this->title);
    }
}
