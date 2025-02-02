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

            $isPermission = $this->user->hasAccessToModule('sales_lead/delete');
        @endphp
        @permission('sales_lead/create')
            <div class="col-6  text-end">
                <div class="form-btn-group">
                    <button type="button" class="btn btn-primary me-0" wire:click="createSalesLead">Create</button>
                </div>
            </div>
        @endpermission
    </div>

    <div class="card" x-data="{expanded : false}">
        <div class="card-header">
            @if ($isDateRangeFilter || $searchId || $branchId || $salesUserId || $departmentId)
                <button class="btn btn-secondary float-end mx-2" type="button" wire:click="clear">
                    Clear
                </button>
            @endif
            <button class="btn btn-primary float-end" type="button" x-on:click="expanded = !expanded">
                Apply Filter
            </button>
        </div>
        <div class="collapse {{ $isCollapse }}" id="collapseExample" :class="expanded ? 'show' : ''">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Select Date</label>
                        <x-date-range-filter class="form-control"></x-date-range-filter>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2">
                        <label>Company</label>
                        <x-select2 class="form-control" name="searchId" id="searchId" wire:model="searchId"
                            placeHolder="{{ $placeHolder }}" parentId="collapseExample">
                            <option value="">Search...</option>
                            @foreach ($this->companyFilterDropDown as $data)
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
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3 d-none">
                        <label>Department</label>
                        <select class="form-select" wire:model.live="departmentId" required>
                            <option value="">Select Department</option>
                            @foreach ($this->departmentList as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label>Progress Stage</label>
                        <select class="form-select" wire:model.live="progressStage" required>
                            <option value="">All</option>
                            @foreach (App\Helpers\AppHelper::PROGRESS_STAGES as $key => $stage)
                                <option value="{{ $key }}">{{ $stage }}</option>
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
            <div class="col-sm-12 col-xxl-12 d-flex justify-content-end">
                <div class=" form-btn-group">
                    <button class="btn btn-success" wire:click="exportFile" wire:loading.attr="disabled">Export</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mt-2 sales-lead-table">
                    <thead>
                        @php
                            $months = [];
                            $currentMonth = \Carbon\Carbon::now()->startOfMonth()->subMonth();

                            for ($i = 0; $i < 5; $i++) {
                                $months[] = $currentMonth->copy()->addMonth()->format('F');
                                $currentMonth = $currentMonth->addMonth(); // Increment the currentMonth by one month
                            }
                        @endphp
                        <tr>
                            <th>Progress</th>
                            <th scope="col">Id</th>
                            <th>Create Date</th>
                            <th>Sales User</th>
                            <th scope="col">FollowUp</th>
                            <th>Company Name</th>
                            <th>Model</th>
                            @foreach ($months as $month)
                                <th>{{ $month }}</th>
                            @endforeach
                            <th>Action</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i <= 10; $i++)
                            @php
                                if(!empty($progressStage) && $progressStage != ($i * 10)){
                                    continue;
                                }
                            @endphp
                            @php
                                $dataItems = $this->listData->where('followups_count', $i);
                                $rowspan = $dataItems->count() > 0 ? $dataItems->count() : 1;
                            @endphp
                            <tr style="border-top: 4px solid #000;" class="border-4-tr">
                                <th scope="row" rowspan="{{ $rowspan + 1 }} ">
                                    <div class="percentDiv">
                                        <div role="progressbar" aria-valuenow="{{ $i * 10 }}" aria-valuemin="0"
                                            aria-valuemax="100" style="--value:{{ $i * 10 }}"></div>
                                    </div>
                                </th>
                            </tr>
                            @forelse ($dataItems as $tr => $data)
                                <tr style="border-top: 1.5px solid #000;">
                                    <td scope="row" style="color:#000;"><strong>{{ $data->id }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d-m-Y') }}
                                    </td>
                                    <td>{{ $data->company->salesUser->name ?? 'All' }}</td>
                                    <td>
                                        <button type="button" wire:click="addLeadFollowup({{ $data->id }})"
                                            class="btn btn-primary-outline">
                                            <i class="fa fa-caret-down" style="font-size:35px;color:darkblue;"></i>
                                        </button>
                                    </td>
                                    <td><a href="{{ route('company-edit', [$data->company->id]) }}"
                                            class="text-decoration-none">{{ $data->company->company_name }}</a></td>
                                    <td>{{ $data->model }}</td>
                                    @php
                                        $dataMonth = \Carbon\Carbon::create()
                                            ->month($data->sales_month)
                                            ->format('F');
                                    @endphp
                                    @foreach ($months as $month)
                                        <td style="text-align: center; color:#000;"><strong>{{ $month == $dataMonth ? $data->qty : '-' }}</strong></td>
                                    @endforeach
                                    <td>
                                        @if ($data->status != 0)
                                            <span
                                                class="font-weight-bold {{ $data->status == 1 ? 'text-success' : 'text-danger' }}">
                                                {{ $data->status == 1 ? 'Achieved' : 'Lost' }}
                                            </span>
                                        @else
                                            <button type="button"
                                                @click="$dispatch('update-lead', { id: {{ $data->id }} , type : 2 })"
                                                {{ $data->status == 0 && $data->followups_count < 10 ? '' : 'disabled' }}
                                                class="btn btn-danger btn-sm text-white mb-2">Lost</button>
                                            <button type="button"
                                                @click="$dispatch('update-lead', { id: {{ $data->id }} , type : 1 })"
                                                {{ $data->followups_count > 9 && $data->status == 0 ? '' : 'disabled' }}
                                                class="btn btn-success btn-sm text-white mb-2">
                                                Achieved
                                            </button>
                                        @endif
                                        @permission('sales_lead/edit')
                                            <button type="button" class="btn btn-primary mb-2"
                                                @click="$dispatch('edit-record', { id: {{ $data->id }} })">
                                                <i class="fa-solid fa-edit"></i>
                                            </button>
                                        @endpermission
                                        @permission('sales_lead/delete')
                                            <button type="button" class="btn btn-danger mb-2"
                                                @click="$dispatch('delete-record', { id: {{ $data->id }} })">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        @endpermission
                                    </td>
                                    <td>{{ $data->followups->isEmpty() ? $data->comment : $data->followups->first()->comment }}
                                    </td>
                                </tr>
                            @empty
                                <tr style="border-top: 4px solid #000;">
                                    <td colspan="13" class="text-center">{{ \App\Helpers\AppHelper::NOT_FOUND }}
                                    </td>
                                </tr>
                            @endforelse
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="leadFollowupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesLeadFolloupTitle">{{ $selectedLeadFollowupTitle }} Lead Followup
                    </h5>
                    <button type="button" class="btn btn-secondary" wire:click="closeFollowUpModal" class="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <form>
                            <table class="table">
                                <tbody>
                                    @foreach ($leadFollowup as $key => $leadFollow)
                                        <tr>
                                            <td><b>{{ $leadFollow['value'] }}%</b></td>
                                            <td>
                                                @if ($leadFollow['is_disabled'])
                                                    <input id="{{ $key }}" type="text"
                                                        class="form-control"
                                                        placeholder="{{ $leadFollow['placeholder'] }}"
                                                        value="{{ $leadFollow['comment'] }}" disabled />
                                                @else
                                                    <input id="{{ $key }}" type="text"
                                                        class="form-control"
                                                        placeholder="{{ $leadFollow['placeholder'] }}"
                                                        wire:model="leadFollowup.{{ $key }}.comment"
                                                        {{ $leadFollow['is_disabled'] ? 'disabled' : '' }} />
                                                @endif
                                            </td>
                                            <td><input type="checkbox" class="form-check-input"
                                                    {{ $leadFollow['is_disabled'] ? 'disabled' : '' }}
                                                    style="width: 25px; height: 25px;"
                                                    wire:model.live="leadFollowup.{{ $key }}.checkbox"></td>
                                            <td>
                                                @if (!empty($leadFollow['comment']) && $leadFollow['is_disabled'])
                                                    <button type="button"
                                                        wire:click="editLeadFollowup({{ $key }})"
                                                        class="btn btn-outline-warning"
                                                        {{ \App\Helpers\AppHelper::isStaffUser($user) ? 'disabled' : '' }}><i
                                                            class="fa-solid fa-pen"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Lead Create Modal --}}

    <div class="modal fade md" id="leadCreateModal" role="dialog" aria-labelledby="leadCreateModalLongTitle"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesLeadFolloupTitle"> Sales Lead Create
                    </h5>
                    <button type="button" class="btn btn-secondary" wire:click="closeSalesLeadModal"
                        class="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="row g-3 needs-validation" novalidate>
                        <div class="col-sm-12 col-lg-12 col-xxl-12 mt-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-3"></h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table w-100">
                                    <thead>
                                        <tr>
                                            <th style="width:25%;" class="text-center">Company</th>
                                            <th style="width:20%;" class="text-center">Model</th>
                                            <th style="width:10%;">QTY</th>
                                            <th style="width:20%;">Sales Month</th>
                                            <th style="width:20%;">Comment</th>
                                            <th style="width:5%;" class="text-end">
                                                <button type="button" class="btn btn-primary"
                                                    wire:click="addSalesLeadField">+</button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($fields as $index => $field)
                                            @php
                                                $key = isset($field['id']) ? $field['id'] : 0;
                                                $lastFollowup = @$field['followups'] ? end($field['followups']) : [];
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="custom-select-2">
                                                        <x-select2 class="form-control"
                                                            name="fields.{{ $index }}.company_id"
                                                            id="fields.{{ $index }}.company_id"
                                                            wire:model="fields.{{ $index }}.company_id"
                                                            placeHolder="{{ $placeHolder }}"
                                                            parentId="leadCreateModal">
                                                            <option value="">Search...</option>
                                                            @foreach ($this->companyFilterDropDown as $data)
                                                                <option value="{{ $data->id }}">
                                                                    {{ $data->company_name }} -
                                                                    {{ $data->customer_code }}</option>
                                                            @endforeach
                                                        </x-select2>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="custom-select-2">
                                                        <x-select2-tag class="form-control"
                                                            name="fields.{{ $index }}.model"
                                                            id="fields.{{ $index }}.model"
                                                            parentId="leadCreateModal">
                                                            <option value="">Search...</option>
                                                            @foreach ($salesLeadModels as $data)
                                                                <option value="{{ $data->name }}">
                                                                    {{ $data->name }}</option>
                                                            @endforeach
                                                        </x-select2-tag>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" placeholder="QTY"
                                                        maxlength="20" wire:model="fields.{{ $index }}.qty"
                                                        style="width: 10ch;"
                                                        @if ($key !== 0) disabled @endif required>
                                                </td>
                                                @php
                                                    $cuurentMonth = date('m');
                                                    $currentDate = new DateTime();

                                                    for ($i = 0; $i < 4; $i++) {
                                                        $currentDate->modify('+1 month'); // Move to the next month
                                                    }
                                                    $lastMonth = $currentDate->format('n'); // Get the last month number
                                                @endphp
                                                <td>
                                                    <select class="form-select"
                                                        wire:model="fields.{{ $index }}.sales_month"
                                                        @if ($key !== 0) disabled @endif required>
                                                        @foreach (range(1, 12) as $monthNumber)
                                                            <option value="{{ $monthNumber }}"
                                                                @if (!in_array($monthNumber, $enabledMonths)) disabled @endif>
                                                                {{ DateTime::createFromFormat('!m', $monthNumber)->format('F') }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea class="form-control" rows="2" placeholder="Comment" wire:model="fields.{{ $index }}.comment"
                                                        @if ($key !== 0) disabled @endif>
                                                    </textarea>
                                                </td>
                                                <td class="text-end">
                                                    @if ($key == 0)
                                                        <button type="button" class="btn btn-danger"
                                                            wire:click="removeSalesLeadField({{ $index }})">X</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="closeSalesLeadModal" class="btn btn-secondary"
                                data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success"
                                wire:click="saveInitiateSalesLead">Initiate Lead</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Lead Create Modal End --}}

    {{-- Edit Saleas Lead Model --}}
    <div class="modal fade md" id="leadEditModal" role="dialog" aria-labelledby="leadEditModalLongTitle"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leadEditModalFolloupTitle"> Sales Lead Create
                    </h5>
                    <button type="button" class="btn btn-secondary" wire:click="closeEditSalesLeadModal"
                        class="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="row g-3 needs-validation" novalidate>
                        <div class="col-sm-12 col-lg-12 col-xxl-12 mt-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-3">Edit Sales Lead Field</h6>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label for="company_id" class="form-label">Company</label>
                                    <div class="custom-select-2">
                                        <x-select2 class="form-control"
                                            name="company_id"
                                            id="company_id"
                                            wire:model="company_id"
                                            placeHolder="{{ $placeHolder }}"
                                            parentId="leadEditModal">
                                            <option value="">Search...</option>
                                            @foreach ($this->companyFilterDropDown as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->company_name }} - {{ $data->customer_code }}</option>
                                            @endforeach
                                        </x-select2>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="model" class="form-label">Model</label>
                                    <div class="custom-select-2">
                                        <x-select2 class="form-control"
                                            name="model"
                                            id="model"
                                            wire:model="model"
                                            parentId="leadEditModal">
                                            <option value="">Search...</option>
                                            @foreach ($salesLeadModels as $data)
                                                <option value="{{ $data->name }}">
                                                    {{ $data->name }}</option>
                                            @endforeach
                                        </x-select2>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label for="qty" class="form-label">QTY</label>
                                    <input type="number" class="form-control" placeholder="QTY" maxlength="20" wire:model="qty" style="width: 10ch;" required>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="sales_month" class="form-label">Sales Month</label>
                                    <select class="form-select" wire:model="sales_month" required>
                                        @foreach (range(1, 12) as $monthNumber)
                                            <option value="{{ $monthNumber }}" @if (!in_array($monthNumber, $enabledMonths)) disabled @endif>
                                                {{ DateTime::createFromFormat('!m', $monthNumber)->format('F') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="comment" class="form-label">Comment</label>
                                    <textarea class="form-control" rows="2" placeholder="Comment" wire:model="comment" required>
                                    </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="closeEditSalesLeadModal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" wire:click="saveEditSalesLead">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Edit Sales Lead Model End --}}
</div>
@script
    <script>
        $wire.on('update-lead', (event) => {
            var type = (event.type == 2) ? 'Lost' : 'Archived';
            var buttonBgColor = (event.type == 2) ? '#d33' : '#146c43';

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to perform this action?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: buttonBgColor,
                cancelButtonColor: "#ccc",
                confirmButtonText: type
            }).then((result) => {
                if (result.isConfirmed) {
                    if (event.type == 2) {
                        Swal.fire({
                            title: 'Provide additional information',
                            html: `
                                <label for="reason">Reason</label>
                                <textarea id="reason" class="swal2-textarea" placeholder="Enter reason" required></textarea>
                                <label for="competitor">Competitor Information</label>
                                <textarea id="competitor" class="swal2-textarea" placeholder="Enter competitor information"></textarea>
                            `,
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: 'Submit',
                            preConfirm: () => {
                                const reason = Swal.getPopup().querySelector('#reason').value;
                                const competitor = Swal.getPopup().querySelector('#competitor')
                                    .value;
                                if (!reason) {
                                    Swal.showValidationMessage('Please enter the reason');
                                }
                                return {
                                    reason: reason,
                                    competitor: competitor
                                };
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $wire.dispatch('updateLead', [event.id, event.type, result.value
                                    .reason, result.value.competitor
                                ]);
                            }
                        });
                    } else {
                        $wire.dispatch('updateLead', [event.id, event.type]);
                    }
                }
            });
        });

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

        $wire.on('edit-record', (event) => {
            $wire.dispatch('edit-sales-lead', [event.id]);
        });


        $wire.on('showLeadFollowupModal', (event) => {
            $('#leadFollowupModal').modal('show');
        });
        $wire.on('hideLeadFollowupModal', (event) => {
            $('#leadFollowupModal').modal('hide');
        });

        $wire.on('showleadCreateModal', (event) => {
            $('#leadCreateModal').modal('show');
        });
        $wire.on('showleadEditModal', (event) => {
            $('#leadEditModal').modal('show');
        });

        $wire.on('hideLeadEditModal', (event) => {
            $wire.set('fields', []);
            $('#leadEditModal').modal('hide');
        });

        $wire.on('hideLeadCreateModal', (event) => {
            $wire.set('fields', []);
            $('#leadCreateModal').modal('hide');
        });
    </script>
@endscript
