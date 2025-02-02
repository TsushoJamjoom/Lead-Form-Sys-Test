<?php

namespace App\Livewire\SalesLead;

use App\Exports\SalesLeadExport;
use App\Models\SalesLead;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Helpers\AppHelper;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\Role;
use App\Models\SalesLeadFollowup;
use App\Models\SalesLeadModel;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SalesLeadList extends Component
{
    use WithPagination, WithFileUploads;

    public $title = 'List Sales Lead';

    // Filter | Sort | Pagination
    public $search;
    public $showSortingIcon = true;
    public $sortDirection = 'desc';
    public $sortBy = 'id';
    public $perPage = 10;
    public $searchId;

    public $user;

    # Filter
    public $isCollapse;
    public $startDateFilter;
    public $endDateFilter;
    public $isDateRangeFilter;
    public $isFilter;
    public $dateFilter;
    public $placeHolder = 'Search...';

    // Followup
    public $leadFollowup = [];
    public $fields = [];
    public $selectedLeadFollowupId;
    public $selectedLeadFollowupTitle;
    public $leadComment;
    public $salesUserId;
    public $branchId;
    public $departmentId;
    public $currentMonth;
    public $enabledMonths = [];

    public $salesLeadModels;
    public $progressStage;
    public $salesLeadId;
    public $company_id;
    public $model;
    public $qty;
    public $comment;
    public $sales_month;

    public function mount()
    {
        $this->user = auth()->user();
        $this->leadFollowup = [
            ['is_disabled' => true, 'placeholder' => 'Received Inquiry', 'comment' => '', 'value' => 10, 'checkbox' => false],
            ['is_disabled' => true, 'placeholder' => 'Submit quotation', 'comment' => '', 'value' => 20, 'checkbox' => false],
            ['is_disabled' => true, 'placeholder' => 'Customer confirmation', 'comment' => '', 'value' => 30, 'checkbox' => false],
            ['is_disabled' => true, 'placeholder' => 'Finance approval', 'comment' => '', 'value' => 40, 'checkbox' => false],
            ['is_disabled' => true, 'placeholder' => 'PO received', 'comment' => '', 'value' => 50, 'checkbox' => false],
            ['is_disabled' => true, 'placeholder' => 'Payment received/credit approved', 'comment' => '', 'value' => 60, 'checkbox' => false],
            ['is_disabled' => true, 'placeholder' => 'PDI/waiting for vehicle', 'comment' => '', 'value' => 70, 'checkbox' => false],
            ['is_disabled' => true, 'placeholder' => 'Delivery', 'comment' => '', 'value' => 80, 'checkbox' => false],
            ['is_disabled' => true, 'placeholder' => 'Invoice', 'comment' => '', 'value' => 90, 'checkbox' => false],
            ['is_disabled' => true, 'placeholder' => 'Connect to aftersales dept', 'comment' => '', 'value' => 100, 'checkbox' => false],
        ];

        $this->fields = [['company_id' => '', 'model' => '', 'qty' => '', 'sales_month' => Date::now()->format('n'), 'comment' => '']];

        $currentDate = Carbon::now();
        for ($i = 0; $i < 5; $i++) {
            $this->enabledMonths[] = $currentDate->format('n'); // Get the month number
            $currentDate = $currentDate->addMonthsNoOverflow(1); // Move to the next month
        }

        $this->leadCarryForward();

        $this->salesLeadModels = SalesLeadModel::active()->get();
    }

    public function leadCarryForward()
    {
        $prevMonth = Carbon::now()->copy()->subMonth()->format('m');
        $leadData = SalesLead::where('status', 0)->where('sales_month', $prevMonth)->get();
        if (!$leadData->isEmpty()) {
            $currentMonth = Carbon::now()->format('m');
            SalesLead::whereIn('id', $leadData->pluck('id'))
                ->update(['sales_month' => $currentMonth]);
        }
    }


    public function exportFile()
    {
        $name = date('d-m-Y-H-i-s') . '-' . 'saleslead.xlsx';
        return Excel::download(new SalesLeadExport, $name);
    }

    public function collapse()
    {
        $this->isCollapse = empty($this->isCollapse) ? 'show' : NULL;
    }

    public function getCompanyFilterDropDownProperty()
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

    public function getDepartmentListProperty()
    {
        return Department::all();
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
        return $this->redirectRoute('sales-lead-list', navigate: true);
    }

    //Update Lead
    #[On('updateLead')]
    public function updateLead($id, $type, $reason = '', $competitorInfo = '')
    {
        try {
            if ($type == 1) {
                SalesLead::find($id)->update(['status' => $type, 'updated_by' => auth()->id()]);
                session()->flash('success', 'Record successfully updated.');
            }
            if ($type == 2) {
                SalesLead::find($id)->update(['status' => $type, 'reason' => $reason, 'competitor_info' => $competitorInfo, 'updated_by' => auth()->id()]);
                session()->flash('success', 'Record successfully updated.');
            }
            return $this->redirectRoute('sales-lead-list', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

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
        $data = SalesLead::withCount(['followups'])
            ->where('status', 0)
            ->with(['company.salesUser', 'followups' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(1); // Eager load only the latest follow-up
            }])
            ->whereHas('company')
            ->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                $query->whereHas('company', function ($q) {
                    return $q->where('sales_user_id', $this->user->id)
                        ->orWhere('sales_user_id', 0);
                });
            })
            ->when($this->salesUserId != '', function ($query) {
                $query->whereHas('company', function ($q) {
                    return $q->where('sales_user_id', $this->salesUserId);
                });
            })
            ->when(AppHelper::isSalesDeptUser($this->user), function ($query) {
                $query->whereHas('company', function ($q) {
                    return $q->where(function ($q) {
                        $q->whereHas('salesUser', function ($q) {
                            return $q->where('role', '!=', 'TJT');
                        })->orWhere('sales_user_id', 0);
                    });
                });
            })
            ->when(!empty($this->branchId), function ($query) {
                $query->whereHas('company', function ($q) {
                    return $q->where('branch_id', $this->branchId);
                });
            })
            ->when($this->searchId, function ($query) {
                $query->where('company_id', $this->searchId);
            })
            ->when((!empty($this->startDateFilter) && !empty($this->endDateFilter)), function ($query) {
                $query->whereBetween(DB::raw('date(created_at)'), [$this->startDateFilter, $this->endDateFilter]);
            })
            ->when(!empty($this->departmentId), function ($query) {
                $query->whereHas('createdby', function ($q) {
                    return $q->where('department_id', $this->departmentId);
                });
            })
            ->when(!empty($this->progressStage), function ($query) {
                $query->whereHas('followups', function ($q) {
                    return $q->where('value', (int)$this->progressStage);
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $data->get();
    }

    public function addLeadFollowup($id)
    {
        $leadFollowups = SalesLead::with(['followups'])->findOrFail($id);
        $this->selectedLeadFollowupId = $id;
        $this->selectedLeadFollowupTitle = $leadFollowups->model;
        if (!$leadFollowups->followups->isEmpty()) {
            foreach ($leadFollowups->followups->toArray() as $key => $followup) {
                $this->leadFollowup[$key]['comment'] = $followup['comment'];
                $this->leadFollowup[$key]['checkbox'] = true;
            }
            $key = $leadFollowups->followups->count();
            if ($key < 10 && $leadFollowups->status == 0) {
                $this->leadFollowup[$key++]['is_disabled'] = false;
            }
        } elseif ($leadFollowups->status == 0) {
            $this->leadFollowup[0]['is_disabled'] = false;
        }
        $this->dispatch('showLeadFollowupModal');
    }

    public function closeFollowUpModal()
    {
        return $this->redirectRoute('sales-lead-list', navigate: true);
    }

    public function updatedLeadFollowup()
    {
        try {
            foreach ($this->leadFollowup as $key => $followup) {
                if ($followup['checkbox'] && $followup['is_disabled'] == false) {
                    if (empty($followup['comment'])) {
                        session()->flash('error', 'The comment field is required.');
                    } else {
                        SalesLeadFollowup::updateOrCreate([
                            'sales_lead_id' => $this->selectedLeadFollowupId,
                            'value' => $followup['value']
                        ], [
                            'sales_lead_id' => $this->selectedLeadFollowupId,
                            'comment' => $followup['comment'],
                            'value' => $followup['value']
                        ]);
                        $this->leadFollowup[$key]['is_disabled'] = true;
                        session()->flash('success', "Comment added: Successfully followed up on the $this->selectedLeadFollowupTitle model.");
                    }
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
        $this->closeFollowUpMOdal();
    }

    public function editLeadFollowup($key)
    {
        $this->leadFollowup[$key]['is_disabled'] = false;
        $this->leadFollowup[$key]['checkbox'] = false;
    }

    public function addSalesLeadField()
    {
        $this->fields[] = ['company_id' => '', 'model' => '', 'qty' => '', 'sales_month' => Date::now()->format('n'), 'comment' => ''];
    }

    public function removeSalesLeadField($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function createSalesLead()
    {
        $this->fields = [['company_id' => '', 'model' => '', 'qty' => '', 'sales_month' => Date::now()->format('n'), 'comment' => '']];
        $this->dispatch('showleadCreateModal');
    }

    public function closeSalesLeadModal()
    {
        $this->dispatch('hideLeadCreateModal');
    }

    public function saveInitiateSalesLead()
    {

        $validator = Validator::make($this->fields, [
            '*.company_id' => 'required|max:120',
            '*.model' => 'required|max:120',
            '*.qty' => 'required|numeric',
            '*.sales_month' => 'digits_between:1,2',
            '*.comment' => 'nullable|max:255',
        ]);
        if ($validator->fails()) {
            $this->dispatch('alert', type: 'error', message: 'Please fill all required fields and ensure they are valid.');
            return;
        }

        try {
            $companyId = '';
            $unsavedDataWithCompanyId = array_map(function ($item) use ($companyId) {
                if (!isset($item['id'])) {
                    $item['created_at'] = Carbon::now();
                    $item['updated_at'] = Carbon::now();
                    $item['created_by'] = auth()->id();
                    return $item;
                }
            }, $this->fields);
            $unsavedDataWithCompanyId = array_filter($unsavedDataWithCompanyId);
            SalesLead::insert($unsavedDataWithCompanyId);
            $this->dispatch('alert', type: 'success', message: 'Initiate sales lead save successfully.');
            $this->dispatch('hideLeadCreateModal');
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
            $this->dispatch('hideLeadCreateModal');
        }
    }

    public function saveEditSalesLead()
    {
        try {
            $validator = Validator::make([
                'company_id' => $this->company_id,
                'model' => $this->model,
                'qty' => $this->qty,
                'sales_month' => $this->sales_month,
                'comment' => $this->comment,
            ], [
                'company_id' => 'required',
                'model' => 'required',
                'qty' => 'required|numeric',
                'sales_month' => 'required',
                'comment' => 'nullable|max:255',
            ]);
            if ($validator->fails()) {
                $this->dispatch('alert', type: 'error', message: 'Please fill all required fields and ensure they are valid.');
                return;
            }
            SalesLead::find($this->salesLeadId)->update([
                'company_id' => $this->company_id,
                'model' => $this->model,
                'qty' => $this->qty,
                'sales_month' => $this->sales_month,
                'comment' => $this->comment
            ]);
            $this->dispatch('alert', type: 'success', message: 'sales lead updated successfully.');
            $this->dispatch('hideLeadEditModal');
            // ... rest of the code ...
        } catch (ValidationException $e) {
            $this->dispatch('alert', type: 'error', message: 'Please fill all required fields and ensure they are valid.');
            return;
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
            $this->dispatch('hideLeadEditModal');
        }
    }

    public function getSalesUsersProperty()
    {
        return AppHelper::getSalesAnd3sUser();
    }

    public function getBranchListProperty()
    {
        return Branch::all();
    }

    #[On('delete')]
    public function delete($id)
    {
        try {
            SalesLead::find($id)->delete();
            session()->flash('success', 'Record successfully deleted.');
            return $this->redirectRoute('sales-lead-list', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    #[On('edit-sales-lead')]
    public function editSalesLead($id)
    {
        $this->salesLeadId = $id;
        $salesLeadData = SalesLead::findOrFail($id);
        $this->fields = [];
        $this->company_id = $salesLeadData->company_id;
        $this->model = $salesLeadData->model;
        $this->qty = $salesLeadData->qty;
        $this->sales_month = $salesLeadData->sales_month;
        $this->comment = $salesLeadData->comment;
        $this->dispatch('showleadEditModal');
    }

    public function closeEditSalesLeadModal()
    {
        $this->dispatch('hideLeadEditModal');
    }

    public function render()
    {
        return view('livewire.sales-lead.sales-lead-list');
    }
}
