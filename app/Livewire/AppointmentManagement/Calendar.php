<?php

namespace App\Livewire\AppointmentManagement;

use App\Helpers\AppHelper;
use App\Models\Branch;
use App\Models\Company;
use App\Models\CustomerEvent;
use App\Models\Department;
use App\Models\History;
use App\Models\SalesLead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Livewire\Attributes\On;

class Calendar extends Component
{
    public $title = 'Calendar';
    public $events = [];
    public $today;

    public $eventId;
    public $company_id;
    public $visit_date;
    public $visit_time;
    public $user;

    # Filter
    public $isCollapse;
    public $startDateFilter;
    public $endDateFilter;
    public $searchId;
    public $isFilter;
    public $dateFilter;
    public $placeHolder = 'Search...';
    public $departmentId;
    public $branchId;
    public $isEditIdSet;
    public $userId;
    public $noteId;
    public $add_note;
    public $appointmentList;
    public $isDateFilter = false;

    public function mount()
    {
        $this->today = now()->format('Y-m-d');
        $this->user = auth()->user();
        $this->startDateFilter = request('startDate');
        $this->endDateFilter = request('endDate');
        $this->searchId = request('searchId');
        $this->departmentId = request('departmentId');
        $this->branchId = request('branchId');
        $this->userId = request('userId');
        if (!empty($this->searchId) || !empty($this->startDateFilter) || !empty($this->endDateFilter) || !empty($this->departmentId) || !empty($this->branchId) || !empty($this->userId)) {
            $this->isFilter = true;
            $this->isDateFilter = !empty($this->startDateFilter) || !empty($this->endDateFilter);
            $this->isCollapse = 'show';
            //$this->dateFilter = Carbon::parse($this->startDateFilter)->format('d/m/Y') . ' - ' . Carbon::parse($this->endDateFilter)->format('d/m/Y');
        }
        $this->applyDateFilter();
        // else {
        //     $this->startDateFilter = Carbon::now()->startOfMonth();
        //     $this->endDateFilter = Carbon::now()->endOfMonth();
        //     $this->dateFilter = Carbon::now()->startOfMonth()->format('d/m/Y') . ' - ' . Carbon::now()->endOfMonth()->format('d/m/Y');
        // }
        $this->appointmentList = $this->getAppointmentList();
    }

    public function applyDateFilter()
    {
        if (!empty($this->startDateFilter) || !empty($this->endDateFilter)) {
            $this->dateFilter = Carbon::parse($this->startDateFilter)->format('d/m/Y') . ' - ' . Carbon::parse($this->endDateFilter)->format('d/m/Y');
        } else {
            $this->startDateFilter = Carbon::now()->subDays(15);
            $this->endDateFilter = Carbon::now()->endOfMonth()->addDays(7);
            $this->dateFilter = $this->startDateFilter->format('d/m/Y') . ' - ' . $this->endDateFilter->format('d/m/Y');
        }
    }

    public function createAppointment()
    {
        $this->validate([
            'company_id' => 'required',
        ], [
            'company_id.required' => 'The company field is required.',
        ]);
        try {
            if (!empty($this->isEditIdSet)) {
                $updateEvent =  CustomerEvent::findOrFail($this->isEditIdSet)->update([
                    'company_id' => $this->company_id,
                    'visit_date' => $this->visit_date,
                    'visit_time' => $this->visit_time,
                    'created_by' => $this->user->id
                ]);
                session()->flash('success', 'Appointment successfully updated.');
            } else {
                $companyExists = CustomerEvent::where([['company_id', $this->company_id], ['visit_date', '=', $this->visit_date], ['created_by', $this->user->id]])->count();
                if (!empty($companyExists)) {
                    session()->flash('error', 'Appointment already added.');
                } else {

                    $addEvent =  CustomerEvent::create([
                        'company_id' => $this->company_id,
                        'visit_date' => $this->visit_date,
                        'visit_time' => $this->visit_time,
                        'created_by' => $this->user->id
                    ]);
                    session()->flash('success', 'Appointment successfully created.');
                }
            }
            return $this->redirectRoute('calendar', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    #[On('closeModel')]
    public function closeModel()
    {
        $this->dispatch('closeEventModel');
    }

    #[On('eventDelete')]
    public function eventDelete($id)
    {
        try {
            CustomerEvent::where('id', $id)->delete();
            session()->flash('success', 'Appointment successfully deleted.');
            return $this->redirectRoute('calendar', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    #[On('eventEdit')]
    public function eventEdit($id)
    {
        try {
            if ($this->eventId == $id) {
                return;
            }
            $this->isCollapse = null;
            $data = CustomerEvent::findOrFail($id);
            $this->eventId = $data->id;
            $this->company_id = $data->company_id;
            $this->visit_date = $data->visit_date;
            $this->visit_time = $data->visit_time;
            $this->isEditIdSet = $id;
            $this->dispatch('open-modal', id: $this->company_id);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function AddNote()
    {
        if (empty($this->add_note)) {
            $this->dispatch('alert', type: 'error', message: 'The note filed is required.');
            return false;
        }
        try {
            $data = CustomerEvent::where('id', $this->noteId)->first();
            $data->note = $this->add_note;
            $data->save();
            session()->flash('success', 'Note successfully added.');
            return $this->redirectRoute('calendar', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    #[On('showAddNote')]
    public function showAddNote($id)
    {
        try {
            $this->noteId = $id;
            $this->dispatch('open-note-modal', id: $this->company_id);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    #[On('setCompanyId')]
    public function setCompanyId($id)
    {
        $this->company_id = $id;
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

    public function getBranchListProperty()
    {
        return Branch::all();
    }

    public function getAppointmentList()
    {
        $events = [];
        $companies = CustomerEvent::with(['company.salesUser', 'createdBy'])
            // ->calendar()
            ->whereHas('company')
            ->whereNotNull('visit_date')
            ->when($this->searchId, function ($query) {
                $query->where('company_id', $this->searchId);
            })
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
            ->when(!empty($this->departmentId), function ($query) {
                $query->whereHas('createdBy', function ($q) {
                    return $q->where('department_id', $this->departmentId);
                });
            })
            ->when(!empty($this->userId), function ($query) {
                $query->where('created_by', $this->userId);
            })
            ->when(!empty($this->departmentId), function ($query) {
                $query->whereHas('createdBy', function ($q) {
                    return $q->where('department_id', $this->departmentId);
                });
            })
            ->when(!empty($this->branchId), function ($query) {
                $query->whereHas('createdBy', function ($q) {
                    return $q->where('branch_id', $this->branchId);
                });
            })
            ->when((!empty($this->startDateFilter) && !empty($this->endDateFilter)), function ($query) {
                $query->whereBetween(DB::raw('date(visit_date)'), [$this->startDateFilter, $this->endDateFilter]);
            });
        $data = $companies->get();
        foreach ($data as $company) {

            $visitCount = CustomerEvent::where('company_id', $company->company_id)
                ->where('customer_satisfaction', '!=', null)
                ->where('visit_date', '>=', Carbon::now()->subMonths(3)->startOfMonth())
                ->count();
            // Determine visit category
            $visitHigh = false;
            $visitLow = false;
            $visitUnattended = false;
            if ($visitCount > 1) {
                $visitHigh = true;
            } elseif ($visitCount == 1) {
                $visitLow = true;
            } else {
                $visitUnattended = true;
            }

            $salesLeadCountByCompany = SalesLead::where([['company_id', $company->company_id], ['status', 0]])->count();
            $activeSymbol = '';
            $visitSymbol = '';
            if ($salesLeadCountByCompany > 0) {
                $activeSymbol = '<b style="color:yellow;">&nbsp; &#10026;</b>';
            }

            if ($visitHigh) {
                $visitSymbol = '(<i class="fa fa-forward" aria-hidden="true"></i>)';
            } elseif ($visitLow) {
                $visitSymbol = '(<i class="fa fa-play" aria-hidden="true"></i>)';
            } elseif ($visitUnattended) {
                $visitSymbol = '(<i class="fa fa-pause" aria-hidden="true"></i>)';
            }
            $color = '#3788d8';
            $textColor = '#FFF';
            $currentDate = Carbon::now()->startOfDay();
            $visitDate = Carbon::parse($company->visit_date)->startOfDay();
            $isGreater = $currentDate->gt($visitDate);
            $emoji = '';
            $time = !empty($company->visit_time) ? date("h:i a", strtotime($company->visit_time)) . ' | ' : '';
            $satisfaction = $company->customer_satisfaction;
            $appointStatus = 'open';
            $showMissedNote = false;
            $routeReadMore = route('company-edit', ['id' => $company->company->id, 'eventid' => $company->id]);
            if ($isGreater && empty($satisfaction)) {
                $color = '#ba0d04'; // red
                $appointStatus = 'closed';
                $showMissedNote = @$company->note == null && $this->user->id == $company->created_by ? true : false;
            } elseif ($isGreater && !empty($satisfaction)) {
                $color = '#378006'; // green
                $appointStatus = 'done';
                $satisfactionImage = $this->getSatisfaction($satisfaction);
                $emoji =  !empty($satisfactionImage) ? $satisfactionImage . ' | ' : '';
                $routeReadMore = "#";
                if (!$company->histories->isEmpty()) {
                    $history = History::where('company_id', $company->company_id)->latest()->first();
                    $routeReadMore = route('history-view', $history->id);
                }
            } elseif (!$isGreater && !empty($satisfaction)) {
                $satisfactionImage = $this->getSatisfaction($satisfaction);
                $emoji = !empty($satisfactionImage) ? $satisfactionImage . ' | ' : '';
            }
            // Make Html View
            $html = View::make('event-data', [
                'data' => $company,
                'visitTime' => $time,
                'appointStatus' => $appointStatus,
                'routeReadMore' => $routeReadMore,
                'showMissedNote' => $showMissedNote,
                'user' => $this->user,
                'salesLeadCountByCompany' => $salesLeadCountByCompany
            ])->render();

            $title = $emoji . $time . $company->company->customer_code . '-' . $company->company->company_name . $activeSymbol . ' ' . $visitSymbol;

            if (!empty($company->createdBy->profile_photo)) {
                $path = asset('storage/user-profile/' . $company->createdBy->profile_photo);
                $image = '<img src="' . $path . '" width="100px" height="100px" alt="Avatar" class="rounded-circle profile">';
                $title = $title . ' ' . $image;
            }

            $newVisitDateTime = $visitDate->toIso8601String();
            if (!blank($company->visit_time)) {
                $newDateTime = $company->visit_date . ' ' . $company->visit_time;
                $newVisitDateTime = Carbon::createFromFormat('Y-m-d H:i', $newDateTime)->toIso8601String();
            }
            $events[] = [
                'title' => $title,
                // 'start' => $company->visit_date,
                'start' => $newVisitDateTime,
                // 'url' => route('company-add', $company->id),
                'color' => $color,
                'textColor' => $textColor,
                'description' => $html,
            ];
        }
        return $events;
    }

    public function getSatisfaction($level)
    {
        $image = "";
        if ($level == 5) {
            $image = "assets/images/Rating-5.png";
        } elseif ($level == 4) {
            $image = "assets/images/Rating-4.png";
        } elseif ($level == 3) {
            $image = "assets/images/Rating-3.png";
        } elseif ($level == 2) {
            $image = "assets/images/Rating-2.png";
        } elseif ($level == 1) {
            $image = "assets/images/Rating-1.png";
        } elseif ($level == 6) {
            $image = "assets/images/Rating-6.png";
        }
        return !empty($image) ? '<img src="' . asset($image) . '" width="15px" class="emoji">' : '';
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
        $this->filterData();
    }

    public function updatedSearchId()
    {
        $this->filterData();
    }

    public function updatedDepartmentId()
    {
        $this->filterData();
    }

    public function updatedBranchId()
    {
        $this->filterData();
    }

    public function updatedUserId()
    {
        $this->filterData();
    }

    public function filterData()
    {
        return $this->redirectRoute('calendar', [
            'startDate' => $this->startDateFilter,
            'endDate' => $this->endDateFilter,
            'searchId' => $this->searchId,
            'departmentId' => $this->departmentId,
            'branchId' => $this->branchId,
            'userId' => $this->userId,
        ], navigate: true);
    }

    public function getUserListProperty()
    {
        return User::when(AppHelper::isSalesDeptUser($this->user), function ($query) {
            return $query->where('role', '!=', 'TJT');
        })->orderBy('created_at', 'desc')->get();
    }

    public function clear()
    {
        return $this->redirectRoute('calendar', navigate: true);
    }

    public function render()
    {
        return view('livewire.appointment-management.calendar');
    }
}
