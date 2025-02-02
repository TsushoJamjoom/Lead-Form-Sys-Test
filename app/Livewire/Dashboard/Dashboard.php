<?php

namespace App\Livewire\Dashboard;

use App\Exports\DashboardReportExport;
use App\Exports\SalesPerformanceReport;
use App\Helpers\AppHelper;
use App\Models\Branch;
use App\Models\Company;
use App\Models\CustomerEvent;
use App\Models\Department;
use App\Models\Role;
use App\Models\SalesLead;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\On;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class Dashboard extends Component
{
    public $title = 'Dashboard';

    public $isCollapse;
    public $userId;
    public $branchId;
    public $departmentId;
    public $categoryBy;

    public $startDateFilter;
    public $endDateFilter;
    public $isDateRangeFilter;
    public $filterBy = 'all';

    public $user;
    public $isFilterClicked = false;

    public function mount()
    {
        $this->user = auth()->user();
        $this->isCollapse = request('isCollapse', $this->isCollapse);
        $this->startDateFilter = request('startDateFilter', $this->startDateFilter);
        $this->endDateFilter = request('endDateFilter', $this->endDateFilter);
        $this->filterBy = request('filterBy', $this->filterBy);
        $this->userId = request('userId', $this->userId);
        $this->branchId = request('branchId', $this->branchId);
        $this->departmentId = request('departmentId', $this->departmentId);
        $this->isFilterClicked = request('isFilterClicked', $this->isFilterClicked);
    }

    public function getCustomerDataProperty()
    {
        // Customers Frequency Data
        $isFilter = false;
        if (($this->filterBy == 'all' || $this->filterBy == 'customer') && $this->isFilterClicked) {
            $currentDate = !empty($this->startDateFilter) ? Carbon::parse($this->startDateFilter) : Carbon::now();
            $endDate = !empty($this->endDateFilter) ? Carbon::parse($this->endDateFilter)->copy() : $currentDate->copy()->subMonths(3);
            $startOfMonth = $currentDate->copy()->startOfMonth();
            $threeMonthsAgo = $endDate;
            $isFilter = true;
        } else {
            $currentDate = Carbon::now();
            $startOfMonth = $currentDate->copy()->startOfMonth();
            $threeMonthsAgo = $currentDate->copy()->subMonths(3);
        }

        $allData = CustomerEvent::whereHas('company', function ($query) {
            $query->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                return $query->where('sales_user_id', $this->user->id)
                    ->orWhere('sales_user_id', 0);
            });
        })->when(!empty($this->userId) && $isFilter, function ($query) {
            $query->where('created_by', $this->userId);
        })->when(!empty($this->departmentId) && $isFilter, function ($query) {
            $query->whereHas('createdBy', function ($q) {
                return $q->where('department_id', $this->departmentId);
            });
        })->when(!empty($this->branchId) && $isFilter, function ($query) {
            $query->whereHas('createdBy', function ($q) {
                return $q->where('branch_id', $this->branchId);
            });
        })->get();

        $companyIds = $allData->pluck('company_id')
            ->unique();

        $highFrequency = 0;
        $lowFrequency = 0;
        $unattended = 0;

        foreach ($companyIds as $companyId) {
            $events = $allData->where('company_id', $companyId);

            $currentMonthEvents = $events->filter(function ($event) use ($startOfMonth, $currentDate) {
                return $event->customer_satisfaction !== null && Carbon::parse($event->visit_date)->between($startOfMonth, $currentDate);
            });
            $lastThreeMonthsEvents = $events->filter(function ($event) use ($threeMonthsAgo, $currentDate) {
                return $event->customer_satisfaction == null && Carbon::parse($event->visit_date)->between($threeMonthsAgo, $currentDate);
            });
            if ($currentMonthEvents->count() >= 2) {
                $highFrequency++;
            }
            if ($currentMonthEvents->count() == 1) {
                $lowFrequency++;
            }
            if ($lastThreeMonthsEvents->count() > 0) {
                $unattended++;
            }
        }

        $totalCustomers = $highFrequency + $lowFrequency + $unattended;

        return [$highFrequency, $lowFrequency, $unattended, $totalCustomers];
    }

    public function getCreatedActivityProperty()
    {
        // SalesLead and Ticket Activity
        $isFilter = true;
        if (($this->filterBy != 'all' || $this->filterBy != 'sales_lead_ticket') && $this->isFilterClicked == false) {
            $isFilter = false;
        }

        $createdSalesLeads = SalesLead::when(!empty($this->userId) && $isFilter, function ($query) {
            $query->where('created_by', $this->userId);
        })->when(!empty($this->departmentId) && $isFilter, function ($query) {
            $query->whereHas('createdBy', function ($q) {
                return $q->where('department_id', $this->departmentId);
            });
        })->when(!empty($this->branchId) && $isFilter, function ($query) {
            $query->whereHas('createdBy', function ($q) {
                return $q->where('branch_id', $this->branchId);
            });
        })->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
            $query->whereHas('company', function ($q) {
                return $q->where('sales_user_id', $this->user->id)
                    ->orWhere('sales_user_id', 0);
            });
        })->when((!empty($this->startDateFilter) && !empty($this->endDateFilter) && $isFilter), function ($query) {
            $query->whereBetween(DB::raw('date(created_at)'), [$this->startDateFilter, $this->endDateFilter]);
        })
            ->count();


        $createdTickets = Ticket::when(!empty($this->userId), function ($query) {
            $query->where('created_by', $this->userId);
        })->when(!empty($this->departmentId), function ($query) {
            $query->whereHas('createdUser', function ($q) {
                return $q->where('department_id', $this->departmentId);
            });
        })->when(!empty($this->branchId), function ($query) {
            $query->whereHas('createdUser', function ($q) {
                return $q->where('branch_id', $this->branchId);
            });
        })->when((!empty($this->startDateFilter) && !empty($this->endDateFilter)), function ($query) {
            $query->whereBetween(DB::raw('date(created_at)'), [$this->startDateFilter, $this->endDateFilter]);
        })->when($this->user->character->slug == AppHelper::STAFF, function ($query) {
            $query->where(function ($q) {
                return $q->where('tickets.user_id', $this->user->id)
                    ->orWhere('tickets.created_by', $this->user->id)
                    ->orWhere('tickets.user_id', 0);
            });
        })
            ->count();

        $activeExecutives = CustomerEvent::select(DB::raw('count(DISTINCT created_by) as total'))
            ->when(!empty($this->userId), function ($query) {
                $query->where('created_by', $this->userId);
            })->when(!empty($this->departmentId), function ($query) {
                $query->whereHas('createdBy', function ($q) {
                    return $q->where('department_id', $this->departmentId);
                });
            })->when(!empty($this->branchId), function ($query) {
                $query->whereHas('createdBy', function ($q) {
                    return $q->where('branch_id', $this->branchId);
                });
            })->when((!empty($this->startDateFilter) && !empty($this->endDateFilter)), function ($query) {
                $query->whereBetween(DB::raw('date(created_at)'), [$this->startDateFilter, $this->endDateFilter]);
            })->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                $query->whereHas('company', function ($q) {
                    return $q->where('sales_user_id', $this->user->id)
                        ->orWhere('sales_user_id', 0);
                });
            })
            ->get();

        return [$createdSalesLeads, $createdTickets, $activeExecutives->sum('total')];
    }

    public function getMonthVisitActivityProperty()
    {
        // Month Visit Activity
        $isFilter = true;
        if (($this->filterBy != 'all' || $this->filterBy != 'month_visit') && $this->isFilterClicked == false) {
            $isFilter = false;
        }

        $companies = CustomerEvent::with(['company', 'createdBy'])
            ->whereNotNull('visit_date')
            ->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                $query->whereHas('company', function ($q) {
                    return $q->where('sales_user_id', $this->user->id)
                        ->orWhere('sales_user_id', 0);
                });
            })->when(!empty($this->userId) && $isFilter, function ($query) {
                $query->where('created_by', $this->userId);
            })->when(!empty($this->departmentId) && $isFilter, function ($query) {
                $query->whereHas('createdBy', function ($q) {
                    return $q->where('department_id', $this->departmentId);
                });
            })->when(!empty($this->branchId) && $isFilter, function ($query) {
                $query->whereHas('createdBy', function ($q) {
                    return $q->where('branch_id', $this->branchId);
                });
            })->when((!empty($this->startDateFilter) && !empty($this->endDateFilter) && $isFilter), function ($query) {
                $query->whereBetween(DB::raw('date(visit_date)'), [$this->startDateFilter, $this->endDateFilter]);
            });

        $data = $companies->get();
        $planned = 0;
        $actual = 0;
        $missed = 0;
        foreach ($data as $company) {

            $currentDate = Carbon::now()->startOfDay();
            $visitDate = Carbon::parse($company->visit_date)->startOfDay();
            $isGreater = $currentDate->gt($visitDate);
            $satisfaction = $company->customer_satisfaction;

            if ($isGreater && empty($satisfaction)) {
                $missed += 1;
            } elseif ($isGreater && !empty($satisfaction)) {
                $actual += 1;
            } elseif (!$isGreater && !empty($satisfaction)) {
                $planned += 1;
            } else {
                $planned += 1;
            }
        }

        return [$planned, $actual, $missed];
    }

    public function getSalesPerformanceProperty()
    {
        // Sales Performance
        $isFilter = true;
        if (($this->filterBy != 'all' || $this->filterBy != 'month_visit') && $this->isFilterClicked == false) {
            $isFilter = false;
        }

        $roleId = Role::where('slug', AppHelper::STAFF)->first()->id;
        $salesDeptId = Department::where('slug', AppHelper::SALES)->first()->id;
        DB::enableQueryLog();
        $data = User::with([
            'events',
            'salesleads',
            'company'
        ])->withCount(['company' => function ($query) use ($isFilter) {
            // $query->orWhere('sales_user_id', 0);
            // $query->when(!empty($this->startDateFilter) && !empty($this->endDateFilter) && $isFilter, function ($q) {
            //     return $q->whereBetween('created_at', [$this->startDateFilter, $this->endDateFilter]);
            // });
        }, 'events' => function ($query) use ($isFilter) {
            $query->when(!empty($this->startDateFilter) && !empty($this->endDateFilter) && $isFilter, function ($q) {
                $q->whereBetween('visit_date', [$this->startDateFilter, $this->endDateFilter]);
            });
        }, 'salesleads' => function ($query) use ($isFilter) {
            $query->when(!empty($this->startDateFilter) && !empty($this->endDateFilter) && $isFilter, function ($q) {
                return $q->whereBetween('created_at', [$this->startDateFilter, $this->endDateFilter]);
            });
        }, 'ticketAssignedMe' => function ($query) use ($isFilter) {
            $query->where('status', 0)->when(!empty($this->startDateFilter) && !empty($this->endDateFilter) && $isFilter, function ($q) {
                return $q->whereBetween('created_at', [$this->startDateFilter, $this->endDateFilter]);
            });
        }, 'ticketCreatedByMe' => function ($query) use ($isFilter) {
            $query->where('status', 0)->when(!empty($this->startDateFilter) && !empty($this->endDateFilter) && $isFilter, function ($q) {
                return $q->whereBetween('created_at', [$this->startDateFilter, $this->endDateFilter]);
            });
        }])->when(!empty($this->userId) && $isFilter, function ($query) {
            $query->where('id', $this->userId);
        })->when(!empty($this->departmentId) && $isFilter, function ($query) {
            $query->where('department_id', $this->departmentId);
        })->when(!empty($this->branchId) && $isFilter, function ($query) {
            $query->where('branch_id', $this->branchId);
        })
            ->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                $query->where('id', $this->user->id);
            })
            ->where('role_id', $roleId)
            ->where('department_id', $salesDeptId)
            ->withoutTjt()
            ->get();

        return $data->map(function ($item) {
            $item->totalArchivedSalesLead = $item->salesleads()->where('status', 1)->when(!empty($this->startDateFilter) && !empty($this->endDateFilter), function ($q) {
                return $q->whereBetween('created_at', [$this->startDateFilter, $this->endDateFilter]);
            })->count();
            $item->totalLostSalesLead = $item->salesleads()->where('status', 2)->when(!empty($this->startDateFilter) && !empty($this->endDateFilter), function ($q) {
                return $q->whereBetween('created_at', [$this->startDateFilter, $this->endDateFilter]);
            })->count();
            $item->highFrequency = 0;
            $item->lowFrequency = 0;
            $item->unattended = 0;
            // Customers Frequency Data
            $currentDate = \Carbon\Carbon::now();
            $startOfMonth = $currentDate->copy()->startOfMonth();

            $threeMonthsAgo = $currentDate->copy()->subMonths(3);

            $events = CustomerEvent::whereIn(
                'company_id',
                $item->company->pluck('id')
            )->get();


            $unattendedCount = array_diff($item->company->pluck('id')->toArray(), $events->pluck('company_id')->toArray());

            $item->highFrequency = 0;
            $item->lowFrequency = 0;
            $item->unattended = 0;
            $highFrequencyCompanyId = [];
            foreach ($events as $event) {
                $highFrequencyCompanyId[$event->company_id] = $highFrequencyCompanyId[$event->company_id] ?? 0;
                if ($event->customer_satisfaction != null && \Carbon\Carbon::parse($event->visit_date)->between($startOfMonth, $currentDate)) {
                    $highFrequencyCompanyId[$event->company_id] += 1;
                }
            }

            $item->highFrequency = count(array_filter($highFrequencyCompanyId, function ($value) {
                return $value > 1;
            }));
            $item->lowFrequency = count(array_filter($highFrequencyCompanyId, function ($value) {
                return $value == 1;
            }));

            $item->unattended = count(array_filter($highFrequencyCompanyId, function ($value) {
                return $value == 0;
            }));

            $item->unattended += count($unattendedCount);




            // // $events = $item->events;
            // $currentMonthEvents = $events->map(function ($events) use (
            //     $startOfMonth,
            //     $currentDate,
            // ) {
            //     $isTrue = $events->customer_satisfaction != null &&
            //         \Carbon\Carbon::parse($events->visit_date)->between(
            //             $startOfMonth,
            //             $currentDate,
            //         );
            //     dd($isTrue);
            // });
            // $lastThreeMonthsEvents = $events->filter(function ($event) use (
            //     $threeMonthsAgo,
            //     $currentDate,
            // ) {
            //     return \Carbon\Carbon::parse($event->visit_date)->between(
            //         $threeMonthsAgo,
            //         $currentDate,
            //     );
            // });

            // if ($lastThreeMonthsEvents->isEmpty()) {
            //     $item->unattended = 1;
            // } else {
            //     $item->unattended = $lastThreeMonthsEvents->filter(function ($event) {
            //         return $event->customer_satisfaction == null;
            //     })->count();
            // }
            // if ($currentMonthEvents->count() >= 2) {
            //     $item->highFrequency = $currentMonthEvents->count();
            // }
            // if ($currentMonthEvents->count() == 1) {
            //     $item->lowFrequency = $currentMonthEvents->count();
            // }
            // if ($lastThreeMonthsEvents->count() > 0) {
            //     $item->unattended = $lastThreeMonthsEvents->count();
            // }


            $item->total = $item->company_count + $item->events_count + $item->highFrequency + $item->lowFrequency + $item->unattended + $item->salesleads_count +  ($item->ticket_assigned_me_count + $item->ticket_created_by_me_count);

            $profilePic = 'https://img.freepik.com/free-vector/businessman-character-avatar-isolated_24877-60111.jpg';
            $file = 'storage/user-profile/' . $item->profile_photo;
            if (!empty($item->profile_photo) && file_exists($file)) {
                $profilePic = asset($file);
            }

            return [
                'name' => $item->name . ' ' . $item->id,
                'profile_photo' => $profilePic,
                'company_count' => $item->company_count,
                'events_count' => $item->events_count,
                'highFrequency' => $item->highFrequency,
                'lowFrequency' => $item->lowFrequency,
                'unattended' => $item->unattended,
                'salesleads_count' => $item->salesleads_count,
                'ticketCount' => $item->ticket_assigned_me_count + $item->ticket_created_by_me_count,
                'total' => $item->total,
                'totalArchivedSalesLead' => $item->totalArchivedSalesLead,
                'totalLostSalesLead' => $item->totalLostSalesLead
            ];
        });
    }
    public function getTicketsDatasetProperty()
    {
        // Ticket Data
        $isFilter = true;
        if (($this->filterBy != 'all' || $this->filterBy != 'tickets') && $this->isFilterClicked == false) {
            $isFilter = false;
        }

        $departments = Department::when(!empty($this->departmentId) && $isFilter, function ($query) {
            $query->where('id', $this->departmentId);
        })->get();

        $departmentColors = [
            'rgba(255,192,0,255)',
            'rgba(97,203,244,255)',
            'rgba(216,110,204,255)',
            'rgba(15,158,213,255)',
            'rgba(71,212,90, 255)',
        ];

        $colorIndex = 0;
        $datasets = [];
        foreach ($departments as $department) {
            $departmentName = $department->name;
            $counts = [
                'Created' => 0,
                'Pending' => 0,
                'In Process' => 0,
                'Completed' => 0
            ];

            $tickets = Ticket::where('dept_id', $department->id)
                ->when(!empty($this->branchId), function ($query) {
                    $query->whereHas('createdUser', function ($q) {
                        return $q->where('branch_id', $this->branchId);
                    });
                })->when((!empty($this->startDateFilter) && !empty($this->endDateFilter)), function ($query) {
                    $query->whereBetween(DB::raw('date(created_at)'), [$this->startDateFilter, $this->endDateFilter]);
                })->when($this->user->character->slug == AppHelper::STAFF, function ($query) {
                    $query->where(function ($q) {
                        return $q->where('tickets.user_id', $this->user->id)
                            ->orWhere('tickets.created_by', $this->user->id)
                            ->orWhere('tickets.user_id', 0);
                    });
                });

            $tickets = $tickets->get();

            foreach ($tickets as $ticket) {

                $counts['Created'] += 1;

                switch ($ticket->status) {
                    case 0:
                        $counts['Pending'] += 1;
                        break;
                    case 1:
                        $counts['In Process'] += 1;
                        break;
                    case 2:
                        $counts['Completed'] += 1;
                        break;
                }
            }


            $dataset = [
                'label' => $departmentName,
                'data' => [
                    $counts['Created'],
                    $counts['Pending'],
                    $counts['In Process'],
                    $counts['Completed']
                ],
                'backgroundColor' => $departmentColors[$colorIndex],
                'borderColor' => 'rgba(255, 255, 255, 1)',
                'borderWidth' => 1
            ];
            $datasets[] = $dataset;
            $colorIndex = ($colorIndex + 1) % count($departmentColors);
        }
        return $datasets;
    }


    public function getFeedbackDataProperty()
    {
        // Satisfaction Scale
        $isFilter = true;
        if (($this->filterBy != 'all' || $this->filterBy != 'satisfaction_scale') && $this->isFilterClicked == false) {
            $isFilter = false;
        }

        $feedbackData = CustomerEvent::select('customer_satisfaction', DB::raw('count(*) as total'))
            ->groupBy('customer_satisfaction') // Note: changed from 'company_id, customer_satisfaction' to just 'customer_satisfaction' for grouping correctly
            ->where('customer_satisfaction', '!=', null)
            ->when(!empty($this->userId) && $isFilter, function ($query) {
                $query->where('created_by', $this->userId);
            })->when(!empty($this->departmentId) && $isFilter, function ($query) {
                $query->whereHas('createdBy', function ($q) {
                    return $q->where('department_id', $this->departmentId);
                });
            })->when(!empty($this->branchId), function ($query) {
                $query->whereHas('createdBy', function ($q) {
                    return $q->where('branch_id', $this->branchId);
                });
            })->when((!empty($this->startDateFilter) && !empty($this->endDateFilter)), function ($query) {
                $query->whereBetween(DB::raw('visit_date'), [$this->startDateFilter, $this->endDateFilter]);
            })->when(AppHelper::isStaffUser($this->user) && AppHelper::isSalesDeptUser($this->user), function ($query) {
                $query->whereHas('company', function ($q) {
                    return $q->where('sales_user_id', $this->user->id)
                        ->orWhere('sales_user_id', 0);
                });
            })
            ->pluck('total', 'customer_satisfaction')
            ->sortDesc();

        if ($feedbackData->isEmpty()) {
            return [];
        }

        return $feedbackData;
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
        return redirect()->route('dashboard');
    }

    public function collapse()
    {
        $this->isCollapse = empty($this->isCollapse) ? 'show' : NULL;
    }

    public function submitFilter()
    {
        $url = URL::route('dashboard', [
            'startDateFilter' => $this->startDateFilter,
            'endDateFilter' => $this->endDateFilter,
            'userId' => $this->userId,
            'departmentId' => $this->departmentId,
            'branchId' => $this->branchId,
            'filterBy' => $this->filterBy,
            'isCollapse' => $this->isCollapse,
            'isFilterClicked' => true,
        ]);
        return redirect($url);
    }

    public function getDepartmentListProperty()
    {
        return Department::all();
    }

    public function getUserListProperty()
    {
        return User::where('id', '!=', auth()->user()->id)->get();
    }

    public function getBranchListProperty()
    {
        return Branch::all();
    }

    public function getPositionListProperty()
    {
        return Role::all();
    }

    public function exportSalesPerformanceReport()
    {
        if ($this->salesPerformance->isEmpty()) {
            $this->dispatch('alert', type: 'error', message: 'Sales performance report is empty.');
            return;
        }
        $name = date('d-m-Y-H-i-s') . '-' . 'SalesPerformanceReport.xlsx';
        return Excel::download(new SalesPerformanceReport($this->salesPerformance), $name);
    }

    public function exportFile()
    {
        $name = date('d-m-Y-H-i-s') . '-' . 'dashboardreport.xlsx';
        return Excel::download(new DashboardReportExport, $name);
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard');
    }
}
