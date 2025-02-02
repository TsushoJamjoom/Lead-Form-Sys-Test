<div class="container-fluid px-3 px-md-4">
    <div class="row mt-5 mb-2">
        <div class="col-6">
            <h3 class="p-0 mb-0">{{ $title ?? '' }}</h3>
            @if (isset($title))
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" wire:navigate>Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">{{ $title ?? '' }}</li>
                </ol>
            @endif
        </div>
        <div class="col-6  text-end">
         <div class="form-btn-group">
            <a class="btn btn-primary me-0" href="{{ route('user-add') }}" wire:navigate>Create</a>
         </div>
        </div>
    </div>
    <div class="card" x-data="{expanded : false}">
        <div class="card-header">
            @if (!empty($departmentId) || !empty($roleId) || !empty($branchId) || !empty($userId) || $statusFilter != NULL || $isDateRangeFilter)
                <button class="btn btn-secondary float-end mx-2" type="button" wire:click="clear">
                    Clear
                </button>
            @endif
            <button class="btn btn-primary float-end" type="button" x-on:click="expanded = !expanded">
                Apply Filter
            </button>
        </div>
        <div class="collapse {{$isCollapse}}" id="collapseExample" :class="expanded ? 'show' : ''">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Select Date</label>
                        <x-date-range-filter class="form-control"></x-date-range-filter>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2">
                        <label>User</label>
                        <x-select2 class="form-control" name="userId" id="userId" wire:model="userId" parentId="collapseExample">
                            <option value="">Search...</option>
                            @foreach ($this->userList as $usr)
                                <option value="{{ $usr->id }}">{{ $usr->name }}</option>
                            @endforeach
                        </x-select2>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Department</label>
                        <select class="form-select" wire:model.live="departmentId" required>
                            <option value="">Select Department</option>
                            @foreach ($this->departmentList as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Branch</label>
                        <select class="form-select" wire:model.live="branchId" required>
                            <option value="">Select Branch</option>
                            @foreach ($this->branchList as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Role</label>
                        <select class="form-select" wire:model.live="roleId" required>
                            <option value="">Select Role</option>
                            @foreach ($this->positionList as $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Status</label>
                        <select class="form-select" wire:model.live="statusFilter" required>
                            <option value="">Select Status</option>
                            <option value="0">Inactive</option>
                            <option value="1">Active</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
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
                    <div class="flex-grow-1">
                        <!-- Use flex-grow-1 to make the search input fill remaining space -->
                        <input type="text" name="query" class="form-control" placeholder="Search"
                            title="Enter search keyword" wire:model.live.debounce.250ms="search">
                    </div>
                 </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mt-2">
                    <thead>
                        <tr>
                            <th scope="col">Id</th>
                            <th>Date</th>
                            <th wire:click="sortColumn('users.name')" class="cursor-pointer">
                                Name
                                @if ($this->showSortingIcon && $this->sortBy == 'users.name')
                                    @if ($this->sortDirection == 'asc')
                                        <i class="fa-solid fa-caret-up"></i>
                                    @else
                                        <i class="fa-solid fa-caret-down"></i>
                                    @endif
                                @endif
                            </th>
                            <th wire:click="sortColumn('users.email')" class="cursor-pointer">
                                Email
                                @if ($this->showSortingIcon && $this->sortBy == 'users.email')
                                    @if ($this->sortDirection == 'asc')
                                        <i class="fa-solid fa-caret-up"></i>
                                    @else
                                        <i class="fa-solid fa-caret-down"></i>
                                    @endif
                                @endif
                            </th>
                            <th>Department</th>
                            <th>Branch</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>View/Edit/Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->listData as $data)
                            <tr>
                                <th scope="row">{{ $data->id }}</th>
                                <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ $data->email }}</td>
                                <td>{{ $data->dp_name ?? '-' }}</td>
                                <td>{{ $data->bc_name ?? '-' }}</td>
                                <td>{{ $data->r_name ?? '-' }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" wire:click="changeStatus({{ $data->id }})"
                                            type="checkbox" id="flexSwitchCheckChecked"
                                            {{ $data->status ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('user-view', [$data->id]) }}" class="btn btn-info btn-sm text-white"
                                        wire:navigate>
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('user-edit', [$data->id]) }}" class="btn btn-warning btn-sm"
                                        wire:navigate>
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        @click="$dispatch('delete-record', { id: {{ $data->id }} })">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">{{ \App\Helpers\AppHelper::NOT_FOUND }}</td>
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
    </script>
@endscript
