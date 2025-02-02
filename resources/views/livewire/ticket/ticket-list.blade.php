<div class="container-fluid px-3 px-md-4">
    <div class="mt-4 d-flex justify-content-between">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="p-0">{{ $title ?? '' }}</h3>
                @if (isset($title))
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" wire:navigate>Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $title ?? '' }}</li>
                    </ol>
                @endif
            </div>
        </div>
    </div>
    <div class="row mt-3 mb-4">
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="{{ route('company-list') }}" class="text-decoration-none" wire:navigate>
                <div class="card border-left-primary shadow-sm py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Companies</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $this->totalCompany }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-building fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="{{ route('ticket-list', ['all']) }}" class="text-decoration-none" wire:navigate>
                <div class="card border-left-info shadow-sm py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Total Tickets</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $this->totalTickets }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="{{ route('ticket-list', [0]) }}" class="text-decoration-none" wire:navigate>
                <div class="card border-left-secondary shadow-sm py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Pending Tickets</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $this->totalTicketsByStatus(0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-comments fa-2x text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="{{ route('ticket-list', [1]) }}" class="text-decoration-none" wire:navigate>
                <div class="card border-left-warning shadow-sm py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    In Process Tickets</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $this->totalTicketsByStatus(1) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-comments fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3 mb-3">
            <a href="{{ route('ticket-list', [2]) }}" class="text-decoration-none" wire:navigate>
                <div class="card border-left-success shadow-sm py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Completed Tickets</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $this->totalTicketsByStatus(2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-trophy fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="card" x-data="{expanded : false}">
        <div class="card-header">
            @if (
                !empty($companyId) ||
                    !empty($departmentId) ||
                    !empty($userId) ||
                    $statusFilter != null ||
                    $isDateRangeFilter ||
                    !empty($ticketBy))
                <button class="btn btn-secondary float-end mx-2" type="button" wire:click="clear">
                    Clear
                </button>
            @endif
            <button class="btn btn-primary float-end" type="button" x-on:click="expanded = !expanded">
                Apply Filter
            </button>
        </div>
        <div class="collapse" id="collapseExample" :class="expanded ? 'show' : ''">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Select Date</label>
                        <x-date-range-filter class="form-control"></x-date-range-filter>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2">
                        <label>Company</label>
                        <x-select2 class="form-control" name="companyId" id="companyId" wire:model="companyId" parentId="collapseExample">
                            <option value="">Search...</option>
                            @foreach ($this->companyDropDown as $data)
                                <option value="{{ $data->id }}">{{ $data->company_name }} -
                                    {{ $data->customer_code }}</option>
                            @endforeach
                        </x-select2>
                    </div>
                    @if ($this->user->character->slug != \App\Helpers\AppHelper::STAFF)
                        <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                            <label>Department</label>
                            <select class="form-select" wire:model.live="departmentId" required>
                                <option value="">Select Department</option>
                                @foreach ($this->departmentList as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2">
                            <label>Assigned</label>
                            <x-select2 class="form-control" name="userId" id="userId" wire:model="userId" parentId="collapseExample">
                                <option value="">Select User</option>
                                @foreach ($this->userList as $usr)
                                    <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                                @endforeach
                            </x-select2>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2">
                            <label>Created</label>
                            <x-select2 class="form-control" name="createdId" wire:model="createdId" id="createdId"
                                parentId="collapseExample">
                                <option value="">Select User</option>
                                @foreach ($this->userList as $usr)
                                    <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                                @endforeach
                            </x-select2>
                        </div>
                    @else
                        <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                            <label>Ticket By</label>
                            <select class="form-select" wire:model.live="ticketBy" required>
                                <option value="">Select Ticket By</option>
                                <option value="my_assigned">My Assigned</option>
                                <option value="my_created">My Created</option>
                            </select>
                        </div>
                    @endif
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Status</label>
                        <select class="form-select" wire:model.live="statusFilter" required>
                            <option value="">Select Status</option>
                            <option value="0">Pending</option>
                            <option value="1">In Progress</option>
                            <option value="2">Completed</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <h4 class="p-4">Ticket List</h4>
        <div class="row mt-2 p-3 pb-0">
            <div class="col-sm-2 col-xxl-1">
                <select id="perPage" class="form-select" wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-sm-10 col-xxl-11 d-none">
                <div class="dash-ticket-search">
                    <form class="search-form d-flex mb-0" method="POST" action="#">
                        <x-select2 class="form-control" name="searchId" id="searchId" parentId="collapseExample">
                            <option value="">Search...</option>
                            @foreach ($this->companyDropDown as $data)
                                <option value="{{ $data->id }}">{{ $data->company_name }} -
                                    {{ $data->customer_code }}</option>
                            @endforeach
                        </x-select2>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mt-2 tickets-table">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th>Date</th>
                            <th wire:click="sortColumn('companies.company_name')" class="cursor-pointer">
                                Company Name
                                @if ($this->showSortingIcon && $this->sortBy == 'companies.company_name')
                                    @if ($this->sortDirection == 'asc')
                                        <i class="fa-solid fa-caret-up"></i>
                                    @else
                                        <i class="fa-solid fa-caret-down"></i>
                                    @endif
                                @endif
                            </th>
                            @if ($this->user->character->slug != \App\Helpers\AppHelper::STAFF)
                                <th wire:click="sortColumn('departments.name')" class="cursor-pointer">
                                    Department
                                    @if ($this->showSortingIcon && $this->sortBy == 'departments.name')
                                        @if ($this->sortDirection == 'asc')
                                            <i class="fa-solid fa-caret-up"></i>
                                        @else
                                            <i class="fa-solid fa-caret-down"></i>
                                        @endif
                                    @endif
                                </th>
                            @endif
                            <th>Created</th>
                            <th>Assigned</th>
                            <th>Note</th>
                            <th>Status</th>
                            <th>In Process Note</th>
                            <th>Completed Note</th>
                            @permission('ticket/delete')
                                <th>Action</th>
                            @endpermission
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->listData as $data)
                            <tr>
                                <th scope="row">{{ $data->id }}</th>
                                <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                <td><a href="{{ route('company-edit', [$data->company_id]) }}"
                                        class="text-decoration-none">{{ $data->company_name }}</a></td>
                                @if ($this->user->character->slug != \App\Helpers\AppHelper::STAFF)
                                    <td>{{ $data->dept_name }}</td>
                                @endif
                                <td>
                                    {{ $this->user->id == $data->created_by ? 'Me' : $data->created_name }}
                                </td>
                                <td>
                                    {{ $this->user->id == $data->user_id ? 'Me' : $data->user_name }}
                                </td>
                                <td>
                                    @if ($this->user->id == $data->created_by || $this->user->id == $data->user_id)
                                        <textarea class="form-control" wire:model="note.{{ $data->id }}"></textarea>
                                        <button class="btn btn-warning btn-sm float-end"
                                            wire:click="updateNote({{ $data->id }})">Update</button>
                                    @else
                                        <span style="word-break: break-word;">{{ $data->note ?? '-' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        if ($data->status == 0) {
                                            $class = 'text-secondary';
                                        } elseif ($data->status == 1) {
                                            $class = 'text-warning';
                                        } else {
                                            $class = 'text-success';
                                        }
                                    @endphp
                                    @if ($this->user->character->slug != \App\Helpers\AppHelper::STAFF)
                                        <select class="form-select {{ $class }}"
                                            onchange="changeStatus(this, {{ $data->id }})">
                                            <option value="0" class="text-secondary"
                                                {{ $data->status == 0 ? 'selected' : '' }}>Pending</option>
                                            <option value="1" class="text-warning"
                                                {{ $data->status == 1 ? 'selected' : '' }}>In Process</option>
                                            <option value="2" class="text-success"
                                                {{ $data->status == 2 ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    @else
                                        <label class="{{ $class }}">
                                            @if ($data->status == 2)
                                                Completed
                                            @elseif ($data->status == 1)
                                                In Process
                                            @else
                                                Pending
                                            @endif
                                        </label>
                                    @endif
                                </td>
                                <td>
                                    @if ($data->status != 1 && $data->status != 2)
                                        @if ($this->user->character->slug != \App\Helpers\AppHelper::STAFF && !empty($data->in_process_note))
                                            <textarea class="form-control" wire:model="in_process_note.{{ $data->id }}"></textarea>
                                            <button class="btn btn-warning btn-sm float-end"
                                                wire:click="updateInProcessNote({{ $data->id }})">Update</button>
                                        @else
                                            <textarea class="form-control" wire:model="in_process_note.{{ $data->id }}"></textarea>
                                            <button class="btn btn-primary btn-sm float-end"
                                                wire:click="submitInProcessNote({{ $data->id }})">Submit</button>
                                        @endif
                                    @else
                                        @if ($this->user->character->slug != \App\Helpers\AppHelper::STAFF)
                                            <textarea class="form-control" wire:model="in_process_note.{{ $data->id }}"></textarea>
                                            <button class="btn btn-warning btn-sm float-end"
                                                wire:click="updateInProcessNote({{ $data->id }})">Update</button>
                                        @else
                                            <span style="word-break: break-word;">{{ $data->in_process_note }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($data->status != 2)
                                        @if ($this->user->character->slug != \App\Helpers\AppHelper::STAFF && !empty($data->completed_note))
                                            <textarea class="form-control" wire:model="completed_note.{{ $data->id }}"></textarea>
                                            <button class="btn btn-warning btn-sm float-end"
                                                wire:click="updateCompletedNote({{ $data->id }})">Update</button>
                                        @else
                                            <textarea class="form-control" wire:model="completed_note.{{ $data->id }}"></textarea>
                                            <button class="btn btn-success btn-sm float-end"
                                                wire:click="submitCompletedNote({{ $data->id }})">Submit</button>
                                        @endif
                                    @else
                                        @if ($this->user->character->slug != \App\Helpers\AppHelper::STAFF)
                                            <textarea class="form-control" wire:model="completed_note.{{ $data->id }}"></textarea>
                                            <button class="btn btn-warning btn-sm float-end"
                                                wire:click="updateCompletedNote({{ $data->id }})">Update</button>
                                        @else
                                            <span style="word-break: break-word;">{{ $data->completed_note }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex p-2 mx-3">
                                        <button type="button" title="View Ticket Details"
                                            wire:click="showTicketDetails({{ $data->id }})"
                                            class="btn btn-info btn-md mr-2" style="margin-right: 7px"><i
                                                class="fa fa-eye" aria-hidden="true"></i>
                                        </button>
                                        @permission('ticket/delete')
                                            <button type="button" title="Delete" class="btn btn-danger btn-md"
                                                @click="$dispatch('delete-record', { id: {{ $data->id }} })">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        @endpermission
                                        @if ($user->id == $data->created_by)
                                            <button type="button" title="Send Reminder"
                                                wire:click="SendTicketReminder({{ $data->id }})"
                                                class="btn btn-success btn-md ms-2"><i class="fa fa-bell"
                                                    aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $this->user->role != \App\Helpers\AppHelper::DEPARTMENT ? 10 : 8 }}"
                                    class="text-center">{{ \App\Helpers\AppHelper::NOT_FOUND }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body row">
                {{ optional($this->listData)->links('vendor.livewire.bootstrap') }}
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="ticketDetailsModal" tabindex="-1" aria-labelledby="ticketDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketDetailsModalLabel">Ticket Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Ticket details content -->
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Date:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="ticketDate">{{ $ticketDate }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Company Name:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="companyName">{{ $companyName }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Department:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="department">{{ $departmentName }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Assigned:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="assignedPerson">{{ $assignedPerson }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Created:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="createdBy">{{ $createdBy }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Note:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="ticketNote">{{ $ticketNote }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Status:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="ticketStatus">{{ $ticketStatus }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">In Process Note:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="inProcessNote">{{ $inProcessNote }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Completed Note:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="completedNote">{{ $completedNote }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        $wire.on('delete-record', (event) => {
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to delete this record?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#ccc",
                confirmButtonText: "Delete"
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.dispatch('delete', [event.id]);
                }
            });
        });

        document.getElementById('ticketDetailsModal').addEventListener('hidden.bs.modal', function(event) {
            $wire.dispatch('closeModel');
        });

        $wire.on('open-modal', function(event) {
            console.log('event-called');
            var myModal = new bootstrap.Modal(document.getElementById('ticketDetailsModal'), {});
            myModal.show();
        });
    </script>
@endscript
@push('scripts')
    <script>
        function changeStatus(el, id) {
            var value = el.options[el.selectedIndex].value;
            // console.log(el.options[el.selectedIndex].value, id);
            Livewire.dispatch('changeStatus', {
                value: value,
                id: id
            });
        }
    </script>
@endpush
