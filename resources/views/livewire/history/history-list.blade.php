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

        </div>
    </div>
    @php
        $isSalesUser = \App\Helpers\AppHelper::isSalesDeptUser($this->user);
        $isStaffUser = \App\Helpers\AppHelper::isStaffUser($this->user);
    @endphp
    <div class="card" x-data="{expanded : false}">
        <div class="card-header">
            @if ($isDateRangeFilter || !empty($branchId) || !empty($salesUserId))
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
                        <label>Company</label>
                        <x-select2 class="form-control" name="searchId" id="searchId" wire:model="searchId" parentId="collapseExample">
                            <option value="">Search...</option>
                            @foreach ($this->companyDropDown as $data)
                                <option value="{{ $data->id }}">{{ $data->company_name }} - {{ $data->customer_code }}</option>
                            @endforeach
                        </x-select2>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
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
                            <x-select2 class="form-control" name="salesUserId" id="salesUserId" wire:model="salesUserId" parentId="collapseExample">
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
            <div class="col-sm-2 col-xxl-1">
                <select id="perPage" class="form-select" wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
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
                            <th>Branch</td>
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
                            <th>DateTime</th>
                            @if (!$isSalesUser)
                                <th>Sales User</th>
                            @endif
                            <th>Sales Note</th>
                            <th>Spare Parts Note</th>
                            <th>Service Note</th>
                            <th>View/Edit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->listData as $data)
                            <tr>
                                <th scope="row">{{ $data->id }}</th>
                                <td>{{ \Carbon\Carbon::parse($data->created_at)->format('Y-m-d') }}</td>
                                <td>{{ $data->company_name }}</td>
                                <td>{{ $data->customer_code }}</td>
                                <td>{{ $data->branch->name ?? '-' }}</td>
                                <td>{{ $data->email }}</td>
                                <td>{{ $data->mobile_no }}</td>
                                <td>{{ \Carbon\Carbon::parse($data->updated_at)->format('m-d-Y h:iA') }}</td>
                                @if (!$isSalesUser)
                                    <td>{{ $data->salesUser->name ?? 'All' }}</td>
                                @endif
                                <td>{{ $data->sales_note }}</td>
                                <td>{{ $data->spare_note }}</td>
                                <td>{{ $data->service_note }}</td>
                                <td>
                                    <a href="{{ route('history-view', [$data->id]) }}"
                                        class="btn btn-info btn-sm text-white" wire:navigate>
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">{{ \App\Helpers\AppHelper::NOT_FOUND }}</td>
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
