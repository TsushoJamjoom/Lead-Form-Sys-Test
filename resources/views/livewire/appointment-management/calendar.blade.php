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
    <div class="card" x-data="{ expanded: false }">
        <div class="card-header">
            @if ($isFilter)
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
                        <x-date-range-filter class="form-control" wire:model="dateFilter"></x-date-range-filter>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2">
                        <label>Company</label>
                        <x-select2 class="form-control" name="searchId" id="searchId" wire:model="searchId"
                            placeHolder="{{ $placeHolder }}" parentId="collapseExample">
                            <option value="">Search...</option>
                            @foreach ($this->companyFilterDropDown as $data)
                                <option value="{{ $data->id }}" {{ $data->id == $searchId ? 'selected' : '' }}>
                                    {{ $data->company_name }} - {{ $data->customer_code }}</option>
                            @endforeach
                        </x-select2>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-3 mb-3">
                        <label for="branchId">Department</label>
                        <select class="form-select" wire:model.live="departmentId">
                            <option value="">Select Department</option>
                            @foreach ($this->departmentList as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
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
                    @php
                        $isStaffUser = App\Helpers\AppHelper::isStaffUser($this->user);
                        $isSalesDeptUser = App\Helpers\AppHelper::isSalesDeptUser($this->user);
                    @endphp
                    @if (!$isStaffUser || !$isSalesDeptUser)
                        <div class="col-sm-12 col-md-6 col-lg-3 mb-3 custom-select-2">
                            <label>User</label>
                            <x-select2 class="form-control" name="userId" id="userId" wire:model="userId" parentId="collapseExample">
                                <option value="">Search...</option>
                                @foreach ($this->userList as $usr)
                                    <option value="{{ $usr->id }}" {{ $usr->id == $userId ? 'selected' : '' }}>
                                        {{ $usr->name }}</option>
                                @endforeach
                            </x-select2>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div id="calendar" wire:ignore></div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="appointmentModal" aria-labelledby="appointmentModalLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="appointmentModalLabel">Create Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="create-event-form">
                        <div class="row">
                            <div class="col-sm-12 form-group custom-select-2">
                                <label for="company_id">Company</label>
                                <x-select2 class="form-control" name="company_id" wire:model="company_id"
                                    id="company_id" placeHolder="{{ $placeHolder }}" parentId="appointmentModal">
                                    <option value="">Search...</option>
                                    @foreach ($this->companyDropDown as $data)
                                        <option value="{{ $data->id }}"
                                            {{ $data->id == $company_id ? 'selected' : '' }}>{{ $data->company_name }}
                                            - {{ $data->customer_code }}</option>
                                    @endforeach
                                </x-select2>
                                @error('company_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-12 form-group">
                                <label for="visit_date">Visit Date</label>
                                <input type="date" id="visit_date" class="form-control" wire:model="visit_date"
                                    disabled>
                            </div>
                            <div class="col-sm-12 form-group">
                                <label for="visit_time">Visit Time</label>
                                <input type="time" id="visit_time" class="form-control" wire:model="visit_time">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        aria-label="Close">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="createAppointment">Save
                        changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div class="modal fade" id="addNoteModal" aria-labelledby="addNoteModalLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="addNoteModalLabel">Add Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body with Form -->
                <div class="modal-body">
                    <form id="addNoteForm">
                        <div class="mb-3">
                            <label for="noteContent" class="form-label">Note</label>
                            <textarea class="form-control" id="noteContent" wire:model="add_note" rows="3"
                                placeholder="Enter your note here..." required></textarea>

                        </div>
                        <button type="button" wire:click="AddNote" class="btn btn-primary">Save Note</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@script
    <script>
        new FullCalendar.Calendar(document.getElementById('calendar'), {
            // timezone:'local',
            timeZone: 'UTC',
            initialView: "dayGridMonth",
            initialDate: "{{ !empty($startDateFilter) && $isDateFilter ? $startDateFilter  : $today  }}",
            displayEventTime: false,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay',
            },
            events: @json($appointmentList),
            eventContent: function(arg) {
                return {
                    html: '<div class="fc-event-title">' + arg.event.title + '</div>'
                };;
            },
            eventDidMount: function(info) {
                // info.el.querySelectorAll('.fc-event-title')[0].innerHTML = info.event.title;
                // console.log(info.event.title, info.el.querySelectorAll('.fc-event-title')[0].innerHTML);
                info.el.style.background = info.backgroundColor;
                info.el.style.borderColor = info.borderColor;
                info.el.style.color = info.textColor;
                new bootstrap.Popover(info.el, {
                    trigger: 'click',
                    html: true,
                    content: info.event.extendedProps.description
                });
            },
            dateClick: function(info) {
                $wire.set('isCollapse', null);
                $wire.set('visit_date', moment(info.dateStr).format('YYYY-MM-DD'));
                if (!moment(info.dateStr).format('H:m').includes("0:0")) {
                    $wire.set('visit_time', moment(info.dateStr).utc().format('HH:mm'));
                }
                $('.popover').hide();
                $wire.dispatch('open-modal')
                // var myModal = new bootstrap.Modal(document.getElementById('appointmentModal'), {});
                // setTimeout(() => {
                //     $('#company_id').select2({
                //         dropdownParent: $("#appointmentModal")
                //     }).on('change', function(event) {
                //         $wire.set('company_id', event.target.value);
                //     });
                //     myModal.show();
                // }, 800);
            }
        }).render();

        document.getElementById('appointmentModal').addEventListener('hidden.bs.modal', function(event) {
            $('#create-event-form').trigger("reset");
            $wire.dispatch('closeModel');
        });

        $wire.on('open-modal', function(event) {
            var myModal = new bootstrap.Modal(document.getElementById('appointmentModal'), {});
            myModal.show();
            if (event) {
                $('#company_id').val(event.id).trigger('change');
            }
        });

        $wire.on('closeEventModel', function(event) {
            $('appointmentModal').modal('hide');
        });

        document.getElementById('addNoteModal').addEventListener('hidden.bs.modal', function(event) {
            $wire.dispatch('closeModel');
        });
    </script>
@endscript
@push('scripts')
    <script>
        // var myModal = new bootstrap.Modal(document.getElementById('appointmentModal'), {});
        // var myModal1 = new bootstrap.Modal(document.getElementById('addNoteModal'), {});
        // Event Delete
        $(document).on('click', '.event-delete', function(event) {
            event.preventDefault();
            $('.popover').hide();
            Livewire.dispatch('eventDelete', {
                id: $(event.target).attr('id').split("-")[1]
            });
        });

        // Event Edit
        $(document).on('click', '.event-edit', function(event) {
            event.preventDefault();
            Livewire.dispatch('eventEdit', {
                id: $(event.target).attr('id').split("-")[1]
            });
            $('.popover').hide();
            // Livewire.dispatch('open-modal');
            // myModal.show();
            // setTimeout(() => {
            //     $('#company_id').select2({
            //         dropdownParent: $("#appointmentModal")
            //     }).on('change', function(event) {
            //         Livewire.dispatch('setCompanyId', {id: event.target.value});
            //     });
            //     myModal.show();
            // }, 800);
        });
        // Create a single instance of the bootstrap.Modal class
        var addNoteModalInstance = new bootstrap.Modal(document.getElementById('addNoteModal'), {});

        // ...

        $(document).on('click', '.event-add-note', function(event) {
            event.preventDefault();
            Livewire.dispatch('showAddNote', {
                id: $(event.target).attr('id').split("-")[1]
            });
            $('.popover').hide();
            // Reuse the existing instance of the modal
            addNoteModalInstance.show();
        });
    </script>
@endpush
