<?php

namespace App\Livewire\SalesLead;

use App\Exports\SalesLeadHistroyExport;
use App\Helpers\AppHelper;
use App\Models\Company;
use App\Models\SalesLead;
use App\Models\SalesLeadModel;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class SalesLeadHistory extends Component
{
    use WithPagination, WithFileUploads;

    public $title = 'Sales Lead History';

    // Filter | Sort | Pagination
    public $search;
    public $showSortingIcon = true;
    public $sortDirection = 'desc';
    public $sortBy = 'updated_at';
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
    public $status;
    public $progressStage;
    public $salesLeadModels;
    public $enabledMonths = [];


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

        $currentDate = \Carbon\Carbon::now();
        for ($i = 0; $i < 5; $i++) {
            $this->enabledMonths[] = $currentDate->format('n'); // Get the month number
            $currentDate = $currentDate->addMonthsNoOverflow(1); // Move to the next month
        }

        $this->fields = [['company_id' => '', 'model' => '', 'qty' => '', 'sales_month' => Date::now()->format('n'), 'comment' => '']];

        $this->salesLeadModels = SalesLeadModel::active()->get();
    }

    public function exportFile()
    {
        $name = date('d-m-Y-H-i-s') . '-' . 'salesleadhistory.xlsx';
        return Excel::download(new SalesLeadHistroyExport(
            $this->status,
            $this->searchId,
            $this->startDateFilter,
            $this->endDateFilter,
            $this->progressStage,
        ), $name);
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
        return $this->redirectRoute('sales-lead-history', navigate: true);
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
    public function getListDataProperty()
    {
        $data = SalesLead::withCount(['followups'])
            ->where('status', '!=', 0)
            ->with(['company.salesUser', 'followups' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(1); // Eager load only the latest follow-up
            }])
            ->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                $query->whereHas('company', function ($q) {
                    return $q->where('sales_user_id', $this->user->id)
                        ->orWhere('sales_user_id', 0);
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
            ->when(!empty($this->salesUserId), function ($query) {
                $query->whereHas('company', function ($q) {
                    return $q->where('sales_user_id', $this->salesUserId);
                });
            })
            ->when(!empty($this->status), function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->searchId, function ($query) {
                $query->where('company_id', $this->searchId);
            })
            ->when((!empty($this->startDateFilter) && !empty($this->endDateFilter)), function ($query) {
                $query->whereBetween(DB::raw('date(created_at)'), [$this->startDateFilter, $this->endDateFilter]);
            })
            ->when(!empty($this->progressStage), function ($query) {
                $query->whereHas('followups', function ($q) {
                    return $q->where('value', ((int)$this->progressStage));
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $data->paginate($this->perPage);
    }

    public function addLeadFollowup($id)
    {
        $this->isCollapse = null;
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

    //Update Lead History
    #[On('updateLeadHistory')]
    public function updateLead($id, $type, $reason = '', $competitorInfo = '')
    {
        try {
            SalesLead::find($id)->update(['status' => 0, 'updated_by' => auth()->id()]);
            session()->flash('success', 'Record successfully updated.');
            return $this->redirectRoute('sales-lead-history', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function closeFollowUpModal()
    {
        return $this->redirectRoute('sales-lead-history', navigate: true);
    }

    #[On('delete')]
    public function delete($id)
    {
        try {
            SalesLead::find($id)->delete();
            session()->flash('success', 'Record successfully deleted.');
            return $this->redirectRoute('sales-lead-history', navigate: true);
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

    public function render()
    {
        return view('livewire.sales-lead.sales-lead-history');
    }
}
