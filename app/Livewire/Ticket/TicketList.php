<?php

namespace App\Livewire\Ticket;

use App\Events\TicketReminder;
use App\Events\TicketStatusChanged;
use App\Helpers\AppHelper;
use App\Models\Company;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class TicketList extends Component
{
    use WithPagination;

    public $user;

    public $title = 'Tickets';

    // Filter | Sort | Pagination
    public $search;
    public $showSortingIcon = true;
    public $sortDirection = 'desc';
    public $sortBy = 'tickets.created_at';
    public $perPage = 10;
    public $searchId;
    public $status;

    public $in_process_note = [];
    public $completed_note = [];
    public $note = [];

    # Filter
    public $isCollapse;
    public $departmentId;
    public $userId;
    public $statusFilter;
    public $companyId;
    public $startDateFilter;
    public $endDateFilter;
    public $isDateRangeFilter;
    public $ticketBy;
    public $createdId;


    //View Ticket Variables
    public $ticketDate;
    public $companyName;
    public $departmentName;
    public $assignedPerson;
    public $createdBy;
    public $ticketNote;
    public $ticketStatus;
    public $inProcessNote;
    public $completedNote;

    public function mount()
    {
        $this->user = auth()->user();
    }

    public function getTotalCompanyProperty()
    {
        $role = $this->user->character->slug;
        $dept = !empty($this->user->dept) ? $this->user->dept->slug : null;
        return Company::when(($role == AppHelper::STAFF) && ($dept == AppHelper::SALES), function ($query) {
            return $query->where('sales_user_id', $this->user->id);
        })->count();
    }

    public function getTotalTicketsProperty()
    {
        $role = $this->user->character->slug;
        return Ticket::when($role == AppHelper::STAFF, function ($query) {
            return $query->where('dept_id', $this->user->department_id)
                ->where(function ($q) {
                    $q->where('user_id', $this->user->id)
                        ->orWhere('created_by', $this->user->id)
                        ->orWhere('user_id', 0);
                });
        })->count();
    }

    public function totalTicketsByStatus($status)
    {
        $role = $this->user->character->slug;
        return Ticket::when($role == AppHelper::STAFF, function ($query) {
            return $query->where('dept_id', $this->user->department_id)
                ->where(function ($q) {
                    $q->where('user_id', $this->user->id)
                        ->orWhere('created_by', $this->user->id)
                        ->orWhere('user_id', 0);
                });
        })
            ->where('status', $status)
            ->count();
    }

    #[On('changeStatus')]
    public function changeStatus($value, $id)
    {
        try {
            Ticket::where('id', $id)->update(['status' => $value, 'updated_by' => auth()->id()]);
            $this->dispatch('alert', type: 'success', message: 'The status of the ticket has been updated.');
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function getCompanyDropDownProperty()
    {
        return Company::createdMe()
            ->when(AppHelper::isSalesDeptUser($this->user), function ($query) {
                return $query->where(function ($q) {
                    $q->whereHas('salesUser', function ($q) {
                        return $q->where('role', '!=', 'TJT');
                    })->orWhere('sales_user_id', 0);
                });
            })
            ->orderBy('created_at', 'desc')->get();
    }

    public function submitInProcessNote($key)
    {
        try {
            $note = trim($this->in_process_note[$key]) ?? '';
            if (empty($note)) {
                $this->dispatch('alert', type: 'error', message: 'The process note field is required.');
                return;
            }
            $ticket = Ticket::where('id', $key)->with('user')->first();
            $ticket->update(['in_process_note' => $note, 'status' => 1, 'updated_by' => auth()->id()]);
            event(new TicketStatusChanged($ticket, 1, $note));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function updateInProcessNote($key)
    {
        try {
            $note = trim($this->in_process_note[$key]) ?? '';
            if (empty($note)) {
                $this->dispatch('alert', type: 'error', message: 'The process note field is required.');
                return;
            }
            $ticket = Ticket::where('id', $key)->with('user')->first();
            $ticket->update(['in_process_note' => $note, 'updated_by' => auth()->id()]);
            $this->dispatch('alert', type: 'success', message: 'The inprogress note has been updated.');
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function updateNote($key)
    {
        try {
            $note = trim($this->note[$key]) ?? '';
            if (empty($note)) {
                $this->dispatch('alert', type: 'error', message: 'The note field is required.');
                return;
            }
            $ticket = Ticket::where('id', $key)->with('user')->first();
            $ticket->update(['note' => $note, 'updated_by' => auth()->id()]);
            $this->SendTicketReminder($ticket->id);
            $this->dispatch('alert', type: 'success', message: 'The note has been updated.');
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function submitCompletedNote($key)
    {
        try {
            $note = trim($this->completed_note[$key]) ?? '';
            if (empty($note)) {
                $this->dispatch('alert', type: 'error', message: 'The complete note field is required.');
                return;
            }
            $ticket = Ticket::where('id', $key)->with('user')->first();
            $ticket->update(['completed_note' => $note, 'status' => 2, 'updated_by' => auth()->id()]);
            event(new TicketStatusChanged($ticket, 2, $note));
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function updateCompletedNote($key)
    {
        try {
            $note = trim($this->completed_note[$key]) ?? '';
            if (empty($note)) {
                $this->dispatch('alert', type: 'error', message: 'The complete note field is required.');
                return;
            }
            $ticket = Ticket::where('id', $key)->with('user')->first();
            $ticket->update(['completed_note' => $note, 'updated_by' => auth()->id()]);
            $this->dispatch('alert', type: 'success', message: 'The completed note has been updated.');
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
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
        $query = Ticket::select([
            'tickets.id',
            'tickets.company_id',
            'tickets.dept_id',
            'tickets.created_by',
            'tickets.user_id',
            'tickets.status',
            'companies.company_name',
            'create.name as created_name',
            'departments.name as dept_name',
            'departments.slug as dept_slug',
            'tickets.note',
            'tickets.in_process_note',
            'tickets.completed_note',
            'tickets.created_at'
        ])
            ->selectRaw('IFNULL(users.name, "All") as user_name')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->where(function ($q) {
                return $q->when($this->searchId, function ($q) {
                    // return $q->where('companies.company_name', 'like', "%$this->search%");
                    return $q->where('companies.id', $this->searchId);
                });
            })
            ->join('departments', 'tickets.dept_id', '=', 'departments.id')
            ->join('companies', 'tickets.company_id', '=', 'companies.id')
            ->leftJoin('users', 'tickets.user_id', '=', 'users.id')
            ->leftJoin('users as create', 'tickets.created_by', '=', 'create.id')
            ->when(!empty($this->companyId), function ($query) {
                $query->where('companies.id', $this->companyId);
            })
            ->when(!empty($this->userId), function ($query) {
                $query->where('tickets.user_id', $this->userId);
            })
            ->when(!empty($this->createdId), function ($query) {
                $query->where('tickets.created_by', $this->createdId);
            })
            ->when(!empty($this->departmentId), function ($query) {
                $query->where('create.department_id', $this->departmentId);
            })
            ->when(($this->statusFilter != NULL), function ($query) {
                $query->where('tickets.status', $this->statusFilter);
            })
            ->when((!empty($this->startDateFilter) && !empty($this->endDateFilter)), function ($query) {
                $query->whereBetween(DB::raw('date(tickets.created_at)'), [$this->startDateFilter, $this->endDateFilter]);
            })
            ->when(!empty($this->ticketBy), function ($query) {
                if ($this->ticketBy == 'my_assigned') {
                    $query->where('tickets.user_id', auth()->id());
                } else {
                    $query->where('tickets.created_by', auth()->id());
                }
            });

        if ($this->user->character->slug == AppHelper::STAFF) {
            $query->where(function ($q) {
                return $q->where('tickets.user_id', $this->user->id)
                    ->orWhere('tickets.created_by', $this->user->id)
                    ->orWhere('tickets.user_id', 0);
            });
        }

        // if ($this->user->role != AppHelper::SUPER_ADMIN) {
        //     $query->where('tickets.dept_id', $this->user->departmentId);
        //     $query->where(function ($q) {
        //         return $q->where('user_id', $this->user->id)
        //             ->orWhere('user_id', 0);
        //     });
        // }

        // Filter by status
        $query->when(($this->status != null && $this->status != 'all'), function ($query) {
            return $query->where('tickets.status', $this->status);
        });

        $data = $query->paginate($this->perPage);
        $data->map(function ($data) {
            $this->in_process_note[$data->id] = $data->in_process_note;
            $this->completed_note[$data->id] = $data->completed_note;
            $this->note[$data->id] = $data->note;
        });
        return $data;
    }

    public function getDepartmentListProperty()
    {
        return Department::all();
    }

    public function getUserListProperty()
    {
        return User::when(AppHelper::isSalesDeptUser($this->user), function ($query) {
            return $query->where('role', '!=', 'TJT');
        })->orderBy('created_at', 'desc')->get();
    }

    public function collapse()
    {
        $this->isCollapse = empty($this->isCollapse) ? 'show' : NULL;
    }

    public function clear()
    {
        return $this->redirectRoute('ticket-list', navigate: true);
    }

    #[On('dateRangeFilter')]
    public function dateRangeFilter($startDate, $endDate)
    {
        $this->startDateFilter = $startDate;
        $this->endDateFilter = $endDate;
        $this->isDateRangeFilter = true;
    }

    #[On('delete')]
    public function delete($id)
    {
        try {
            Ticket::find($id)->delete();
            session()->flash('success', 'Record successfully deleted.');
            return $this->redirectRoute('ticket-list', navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function SendTicketReminder($ticketId)
    {
        $ticket = Ticket::with(['user', 'createdUser', 'company', 'dept'])->find($ticketId);
        event(new TicketReminder($ticket));
        $this->dispatch('alert', type: 'success', message: 'Reminder sent successfully.');
    }

    #[On('showTicketDetails')]
    public function showTicketDetails($id)
    {
        try {

            $data = Ticket::with(['company', 'createdUser', 'dept', 'user'])->findOrFail($id);
            $this->ticketDate = \Carbon\Carbon::parse($data->created_at)->format('Y-m-d');
            $this->companyName = @$data->company->company_name;
            $this->departmentName = @$data->dept->name;
            $this->assignedPerson = @$data->user->name ?: "All";
            $this->createdBy = @$data->createdUser->name;
            $this->ticketNote = @$data->note ?: "-";
            $this->ticketStatus = $data->status == 1 ? 'In Process' : ($data->status == 2 ? 'Completed' : 'Pending');
            $this->inProcessNote = @$data->in_process_note ?: "-";
            $this->completedNote = @$data->completed_note ?: "-";
            $this->dispatch('open-modal');
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.ticket.ticket-list');
    }
}
