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
        @php
            $isSalesUser = \App\Helpers\AppHelper::isSalesDeptUser($this->user);
            $isStaffUser = \App\Helpers\AppHelper::isStaffUser($this->user);
        @endphp
        @permission('customer/create')
            <div class="col-6  text-end">
                <div class="form-btn-group">
                    <a class="btn btn-primary me-0" href="{{ route('company-create') }}" wire:navigate>Create</a>
                </div>
            </div>
        @endpermission
    </div>
    <div class="card" x-data="{ expanded: false }">
        <div class="card-header">
            @if ($isDateRangeFilter || !empty($searchId) || !empty($branchId) || !empty($salesUserId))
                <button class="btn btn-secondary float-end mx-2" type="button" wire:click="clear">
                    Clear
                </button>
            @endif
            <button class="btn btn-primary float-end" type="button" x-on:click="expanded = ! expanded">
                Apply Filter
            </button>
        </div>
        <div class="collapse {{ $isCollapse }}" id="collapseExample" :class="expanded ? 'show' : ''">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Select Date</label>
                        <x-date-range-filter class="form-control"
                            value="{{ $this->startDateLabel }} - {{ $this->endDateLabel }}"></x-date-range-filter>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2">
                        <label>Company</label>
                        <x-select2 class="form-control" name="searchId" id="searchId" wire:model="searchId" parentId="collapseExample">
                            <option value="">Search...</option>
                            @foreach ($this->companyDropDown as $data)
                                <option value="{{ $data->id }}">{{ $data->company_name }} -
                                    {{ $data->customer_code }}</option>
                            @endforeach
                        </x-select2>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2">
                        <label for="branchId">Branch</label>
                        <select class="form-select" wire:model.live="branchId">
                            <option value="">Select Branch</option>
                            @foreach ($this->branchList as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if (!$isSalesUser || !$isStaffUser)
                        <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2">
                            <label>Sales User</label>
                            <x-select2 class="form-control" name="salesUserId" wire:model="salesUserId" id="salesUserId"
                                parentId="collapseExample">
                                <option value="">Search...</option>
                                <option value="0">All</option>
                                @foreach ($this->salesUsers as $data)
                                    <option value="{{ $data->id }}">{{ $data->name }}</option>
                                @endforeach
                            </x-select2>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="row mt-2 p-3 pb-0">
            <div class="col-sm-2 col-xxl-1 mb-3">
                <select id="perPage" class="form-select" wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-sm-10 col-xxl-11 d-flex justify-content-end">
                <div class=" form-btn-group">
                    @permission('customer/import')
                    <label class="btn btn-info" for="import_file" wire:loading.class="opacity-50">Import</label>
                    <input type="file" class="d-none" id="import_file" wire:model.live="importFile" />
                    @endpermission
                    @permission('customer/export')
                    <button class="btn btn-success" wire:click="exportFile" wire:loading.attr="disabled">Export</button>
                    @endpermission
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
                            <th wire:click="sortColumn('company_name')" class="cursor-pointer">
                                Company Name
                                @if ($this->showSortingIcon && $this->sortBy == 'company_name')
                                    @if ($this->sortDirection == 'asc')
                                        <i class="fa-solid fa-caret-up"></i>
                                    @else
                                        <i class="fa-solid fa-caret-down"></i>
                                    @endif
                                @endif
                            </th>
                            <th wire:click="sortColumn('contact_person')" class="cursor-pointer">
                                Contact Person
                                @if ($this->showSortingIcon && $this->sortBy == 'contact_person')
                                    @if ($this->sortDirection == 'asc')
                                        <i class="fa-solid fa-caret-up"></i>
                                    @else
                                        <i class="fa-solid fa-caret-down"></i>
                                    @endif
                                @endif
                            </th>
                            <th wire:click="sortColumn('customer_code')" class="cursor-pointer">
                                Customer Code
                                @if ($this->showSortingIcon && $this->sortBy == 'customer_code')
                                    @if ($this->sortDirection == 'asc')
                                        <i class="fa-solid fa-caret-up"></i>
                                    @else
                                        <i class="fa-solid fa-caret-down"></i>
                                    @endif
                                @endif
                            </th>
                            <th>Branch</th>
                            @if (!$isSalesUser || !$isStaffUser)
                                <th>
                                    Sales User
                                </th>
                            @endif
                            <th wire:click="sortColumn('email')" class="cursor-pointer">
                                Email
                                @if ($this->showSortingIcon && $this->sortBy == 'email')
                                    @if ($this->sortDirection == 'asc')
                                        <i class="fa-solid fa-caret-up"></i>
                                    @else
                                        <i class="fa-solid fa-caret-down"></i>
                                    @endif
                                @endif
                            </th>
                            <th wire:click="sortColumn('mobile_no')" class="cursor-pointer">
                                Mobile No
                                @if ($this->showSortingIcon && $this->sortBy == 'mobile_no')
                                    @if ($this->sortDirection == 'asc')
                                        <i class="fa-solid fa-caret-up"></i>
                                    @else
                                        <i class="fa-solid fa-caret-down"></i>
                                    @endif
                                @endif
                            </th>
                            <th>View/Edit/Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->listData as $data)
                            <tr>
                                <th scope="row">{{ $data->id }}</th>
                                <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                <td>{{ $data->company_name }}</td>
                                <td>{{ $data->contact_person }}</td>
                                <td>{{ $data->customer_code }}</td>
                                <td>{{ $data->branch->name ?? '-' }}</td>
                                @if (!$isSalesUser || !$isStaffUser)
                                    <td>{{ $data->salesUser->name ?? 'All' }}</td>
                                @endif
                                <td>{{ $data->email }}</td>
                                <td>{{ $data->mobile_no }}</td>
                                <td>
                                    @permission('customer/view')
                                        <a href="{{ route('company-view', [$data->id]) }}"
                                            class="btn btn-info btn-sm text-white" wire:navigate>
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @endpermission
                                    @php
                                        $isPermission = $this->user->hasAccessToModule('customer/create');
                                        $url = route('company-edit', [$data->id]);
                                        if ($isPermission) {
                                            $url = route('company-create', [$data->id]);
                                        }
                                    @endphp
                                    @permission('customer/edit')
                                        <a href="{{ $url }}" class="btn btn-warning btn-sm" wire:navigate>
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endpermission
                                    @if (!App\Helpers\AppHelper::isStaffUser($this->user))
                                        @permission('customer/edit')
                                            <a href="{{ route('company-edit', [$data->id]) }}"
                                                class="btn btn-primary btn-sm" wire:navigate>
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                        @endpermission
                                    @endif
                                    @permission('customer/delete')
                                        <button type="button" class="btn btn-danger btn-sm"
                                            @click="$dispatch('delete-record', { id: {{ $data->id }} })">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endpermission

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
        $wire.on('import-confirm', (event) => {
            Swal.fire({
                title: "Duplicate data found.",
                text: "Do you want to overwrite data?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#ccc",
                confirmButtonText: "Yes",
                cancelButtonText: "No"
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.dispatch('import-overwrite', [event.id]);
                }
            });
        });
    </script>
@endscript
