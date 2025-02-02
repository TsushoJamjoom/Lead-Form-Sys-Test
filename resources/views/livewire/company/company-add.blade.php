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
                <a class="btn btn-outline-dark  me-0" href="{{ App\Helpers\AppHelper::getPreviousUrl('company-list') }}" wire:navigate>Back</a>
            </div>
        </div>
    </div>
    <div class="card p-3" id="topHeader">
        <div class="card-body">
            <form class="custom-css" wire:submit="store">
                <div class="row">
                    <div class="col-sm-12">
                        <div
                            class="d-flex justify-content-center justify-content-sm-around align-items-center flex-wrap gap-2">
                            <div class="compnay-logo">
                                <img src="{{ asset('assets/images/company-logo.png') }}" alt="logo" />
                            </div>

                            <div class="user-logo-preview position-relative">
                                <button class="btn btn-primary" type="button" onclick="triggerBookEvent({{$id}})">Book Appointment</button>
                            </div>

                            <div class="user-logo-preview position-relative">
                                @if (!empty($userLogoName))
                                    <label class="user-logo-area">
                                        <a href="{{ asset('storage/user-logo/' . $userLogoName) }}" target="_blank">
                                            <img id="LogoPreview"
                                                src="{{ asset('storage/user-logo/' . $userLogoName) }}"
                                                alt="logo" />
                                        </a>
                                        <span class="remove-icon"
                                            wire:click="removeUserLogo('{{ $userLogoName }}')">X</span>
                                    </label>
                                @else
                                    <label for="user-logo" class="user-logo-area">
                                        <img id="LogoPreview" src="{{ asset('assets/images/dummy-logo.webp') }}"
                                            alt="logo" />
                                        <input type="file" id="user-logo" class="d-none" wire:model.live="userLogo">
                                    </label>
                                    @error('userLogo')
                                        <br />
                                        <span class="text-danger">The image size should not exceed 1MB.</span>
                                    @enderror
                                @endif
                            </div>

                            <div class="d-block d-sm-flex align-items-center gap-2 search-box">
                                <div class="mb-3 mb-sm-0 ">
                                    <x-select2 class="form-control" name="searchVal" id="searchVal" parentId="topHeader">
                                        <option value="">Search...</option>
                                        @foreach ($this->companyDropDown as $data)
                                            <option value="{{ $data->id }}">{{ $data->company_name }} -
                                                {{ $data->customer_code }}</option>
                                        @endforeach
                                    </x-select2>
                                </div>
                                <a type="button" class="btn btn-warning text-nowrap d-none" id="myModalElShow">Scan QR
                                    Code</a>
                                <a type="button" class="btn btn-dark" onclick="window.print(); setTimeout(function () { var printContents = document.getElementById('printArea').innerHTML; var originalContents = document.body.innerHTML; document.body.innerHTML = printContents; var scale = 0.5; var style = `<style> @media print { body { -webkit-transform: scale(${scale}); transform: scale(${scale}); } }</style>`; document.head.insertAdjacentHTML('beforeend', style); window.print(); window.close(); document.body.innerHTML = originalContents; }, 500);">Print</a>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mt-3 mb-4">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label for="name-f">Compnay name</label>
                                <input type="text" class="form-control" maxlength="50"
                                    wire:model="form.company_name" disabled>
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>Customer code</label>
                                <input type="text" class="form-control" maxlength="20"
                                    wire:model="form.customer_code" disabled>
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>Branch</label>
                                <select class="form-select" wire:model="form.branch_id" disabled>
                                    <option value="">Select Branch</option>
                                    @foreach ($this->branchList as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>CR/ID</label>
                                <input type="text" class="form-control" maxlength="20" wire:model="form.crid">
                                @error('form.crid')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>VAT</label>
                                <input type="text" class="form-control" maxlength="20" wire:model="form.vat">
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>Company Website</label>
                                <input type="text" class="form-control" maxlength="50" wire:model="form.website">
                            </div>
                            <div class="col-sm-5 form-group" x-data="{ expanded: false }" @mouseover.away = "expanded = false">
                                <label for="mb-2 d-block" for="new-cordinates">Coordinates</label>
                                <div class="custom-dropdown w-100" x-on:click="expanded = ! expanded">
                                        <div class="selected-option-location form-control form-select">{{ !empty($selectedCoordinate) ? $selectedCoordinate :'Add Coordinate'}}</div>
                                    <ul class="dropdown-options w-100" :class="expanded ? '' : 'd-none'">
                                        @php
                                            $coordinateCount = count($coordinates);
                                        @endphp
                                        @foreach ($coordinates as $key => $location)
                                            <li class="{{ $selectedCoordinate == $location ?  'selected-option' : ''}}" @click="$dispatch('select-coordinate', '{{$location}}')">{{$location}} <span class="text-danger" wire:click="removeLocation({{$key}})" style="float: right;font-weight: 1000">X</span></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @if (count($coordinates) < 5)
                            <div class="col-sm-1">
                                <button class="btn btn-primary mt-45" type="button"
                                    @click="$dispatch('add-location')">Add</button>
                            </div>
                            @endif
                            <div class="col-sm-12 form-group" bis_skin_checked="1">
                                <label>National Address</label>
                                <input type="text" class="form-control" maxlength="100"
                                    wire:model="form.national_address">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="name-l">Sales Channel contact person</label>
                            <input type="text" class="form-control" maxlength="30"
                                wire:model="form.contact_person">
                            @error('form.contact_person')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Position</label>
                            <input type="text" class="form-control" maxlength="30" wire:model="form.position">
                        </div>
                        <div class="form-group">
                            <label>Mobile No.</label>
                            <input type="text" class="form-control" maxlength="15" wire:model="form.mobile_no">
                            @error('form.mobile_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" maxlength="50" wire:model="form.email">
                            @error('form.email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Sales Man</label>
                            <select class="form-select" wire:model="form.sales_user_id" id="sales_user_id" disabled>
                                <option>Select Sales User</option>
                                @foreach ($this->salesUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                                @if($form['sales_user_id'] == 0)
                                    <option selected value="0">All</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 form-group align-items-center">
                        <label class="me-3 mb-0 d-block mb-2 mb-lg-0">Type of Business</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                wire:model="form.construction">
                            <label class="form-check-label" for="inlineCheckbox1">Construction</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox2"
                                wire:model="form.food">
                            <label class="form-check-label" for="inlineCheckbox2">Food</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3"
                                wire:model="form.rental">
                            <label class="form-check-label" for="inlineCheckbox3">Rental</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox4"
                                wire:model="form.logistics">
                            <label class="form-check-label" for="inlineCheckbox4">Logistics</label>
                        </div>
                        <div
                            class="ps-0 ps-lg-3 me-0 form-check form-check-inline d-inline-flex align-items-center gap-2">
                            <label class="form-check-label mb-0 text-nowrap">Describe other</label>
                            <input class="form-control" type="text" maxlength="30"
                                wire:model="form.describe_other">
                        </div>
                    </div>
                </div>
                <hr class="my-4">

                <div class="row">
                    <div class="col-sm-12 col-lg-8 col-xxl-9">
                        <div class="table-responsive">
                            <table class="table w-100 custom-input-width">
                                <thead>
                                    <tr>
                                        <th>Fleet Details</th>
                                        <th>HINO</th>
                                        <th>ISUZU</th>
                                        <th>FUSO</th>
                                        <th>SITRAK</th>
                                        <th>SANY</th>
                                        <th>SHACMAN</th>
                                        <th>FAW</th>
                                        <th>SINOTRUK</th>
                                        <th>MAN</th>
                                        <th>VOLVO</th>
                                        <th>MERCEDES</th>
                                        <th>UD</th>
                                        <th>OTHER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Pick-up Truck</td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.hino_pick_up"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.isuzu_pick_up"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.fuso_pick_up"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.sitrak_pick_up"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.sany_pick_up"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.shacman_pick_up"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.faw_pick_up">
                                        </td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.sinotruk_pick_up"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.man_pick_up"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.volvo_pick_up"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.mercedes_pick_up"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.ud_pick_up"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.other_pick_up"></td>
                                    </tr>
                                    <tr>
                                        <td>Light Duty Truck </td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.hino_light_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.isuzu_light_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.fuso_light_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.sitrak_light_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.sany_light_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.shacman_light_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.faw_light_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.sinotruk_light_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.man_light_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.volvo_light_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.mercedes_light_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.ud_light_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.other_light_duty_truck"></td>
                                    </tr>
                                    <tr>
                                        <td>Medium Duty Truck</td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.hino_medium_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.isuzu_medium_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.fuso_medium_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.sitrak_medium_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.sany_medium_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.shacman_medium_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.faw_medium_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.sinotruk_medium_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.man_medium_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.volvo_medium_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.mercedes_medium_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.ud_medium_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.other_medium_duty_truck"></td>
                                    </tr>
                                    <tr>
                                        <td>Heavy Duty Truck </td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.hino_heavy_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.isuzu_heavy_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.fuso_heavy_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.sitrak_heavy_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.sany_heavy_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.shacman_heavy_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.faw_heavy_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.sinotruk_heavy_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.man_heavy_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.volvo_heavy_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.mercedes_heavy_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.ud_heavy_duty_truck"></td>
                                        <td><input type="number" class="form-control" min="0" maxlength="10"
                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                wire:model.live.debounce.500ms="form.other_heavy_duty_truck"></td>
                                    </tr>
                                    <tr class="bg-light-gray">
                                        <td><strong>Total</strong></td>
                                        <td>
                                            <input type="text" class="form-control" wire:model="form.hino_total"
                                                disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" wire:model="form.isuzu_total"
                                                disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" wire:model="form.fuso_total"
                                                disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" wire:model="form.sitrak_total"
                                                disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" wire:model="form.sany_total"
                                                disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                wire:model="form.shacman_total" disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" wire:model="form.faw_total"
                                                disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                wire:model="form.sinotruk_total" disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" wire:model="form.man_total"
                                                disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" wire:model="form.volvo_total"
                                                disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                wire:model="form.mercedes_total" disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" wire:model="form.ud_total"
                                                disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" wire:model="form.other_total"
                                                disabled>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Oldest Modal Year Range</td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.hino_oldest">
                                        </td>
                                        <td><input type="text" class="form-control"
                                                wire:model="form.isuzu_oldest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.fuso_oldest">
                                        </td>
                                        <td><input type="text" class="form-control"
                                                wire:model="form.sitrak_oldest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.sany_oldest">
                                        </td>
                                        <td><input type="text" class="form-control"
                                                wire:model="form.shacman_oldest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.faw_oldest">
                                        </td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.sinotruk_oldest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.man_oldest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.volvo_oldest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.mercedes_oldest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.ud_oldest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.other_oldest"></td>
                                    </tr>
                                    <tr>
                                        <td>Latest Modal Year Range</td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.hino_latest">
                                        </td>
                                        <td><input type="text" class="form-control"
                                                wire:model="form.isuzu_latest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.fuso_latest">
                                        </td>
                                        <td><input type="text" class="form-control"
                                                wire:model="form.sitrak_latest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.sany_latest">
                                        </td>
                                        <td><input type="text" class="form-control"
                                                wire:model="form.shacman_latest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.faw_latest">
                                        </td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.sinotruk_latest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.man_latest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.volvo_latest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.mercedes_latest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.ud_latest"></td>
                                        <td><input type="text" class="form-control" maxlength="20"
                                                wire:model="form.other_latest"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-12 col-lg-4 col-xxl-3">
                        <div class="mb-3 mt-3 mt-lg-0">
                            <h6>Cities of operation</h6>
                        </div>
                        <div class="row ps-3 citeis-list">
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox1" value="Jeddah"
                                    wire:model="form.jeddah">
                                <label class="form-check-label" for="Checkbox1">Jeddah</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox2" value="Makkah"
                                    wire:model="form.makkah">
                                <label class="form-check-label" for="Checkbox2">Makkah</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox3" value="Najran"
                                    wire:model="form.najran">
                                <label class="form-check-label" for="Checkbox3">Najran</label>
                            </div>

                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox4" value="Madina"
                                    wire:model="form.madina">
                                <label class="form-check-label" for="Checkbox4">Madina</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox5" value="Alyth"
                                    wire:model="form.alyth">
                                <label class="form-check-label" for="Checkbox5">Alyth</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox6" value="Jizan"
                                    wire:model="form.jizan">
                                <label class="form-check-label" for="Checkbox6">Jizan</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox7" value="Riyadh"
                                    wire:model="form.riyadh">
                                <label class="form-check-label" for="Checkbox7">Riyadh</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox8" value="Yanbu"
                                    wire:model="form.yanbu">
                                <label class="form-check-label" for="Checkbox8">Yanbu</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox9" value="Khamis"
                                    wire:model="form.khamis">
                                <label class="form-check-label" for="Checkbox9">Khamis</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox10" value="Dammam"
                                    wire:model="form.dammam">
                                <label class="form-check-label" for="Checkbox10">Dammam</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox11" value="Buraidah"
                                    wire:model="form.buraidah">
                                <label class="form-check-label" for="Checkbox11">Buraidah</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox12" value="Tabuk"
                                    wire:model="form.tabuk">
                                <label class="form-check-label" for="Checkbox12">Tabuk</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox13" value="Al-khobar"
                                    wire:model="form.al_khobar">
                                <label class="form-check-label" for="Checkbox13">Al-khobar</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox14" value="Hail"
                                    wire:model="form.hail">
                                <label class="form-check-label" for="Checkbox14">Hail</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox15" value="Taif"
                                    wire:model="form.taif">
                                <label class="form-check-label" for="Checkbox15">Taif</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox16" value="Abha"
                                    wire:model="form.abha">
                                <label class="form-check-label" for="Checkbox16">Abha</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox17" value="Al-Baha"
                                    wire:model="form.al_baha">
                                <label class="form-check-label" for="Checkbox17">Al-Baha</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox18" value="Neom"
                                    wire:model="form.neom">
                                <label class="form-check-label" for="Checkbox18">Neom</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox19" value="Hafr Batin"
                                    wire:model="form.hafr_batin">
                                <label class="form-check-label" for="Checkbox19">Hafr Batin</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox20" value="Alqassim"
                                    wire:model="form.alqassim">
                                <label class="form-check-label" for="Checkbox20">Alqassim</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox21" value="Jubail"
                                    wire:model="form.jubail">
                                <label class="form-check-label" for="Checkbox21">Jubail</label>
                            </div>
                            <div class="col-sm-12 ps-0">
                                <label class="mb-2">Other Cities</label>
                                <input class="form-control" type="text" maxlength="20"
                                    wire:model="form.other_cities">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-sm-4 mb-3">
                        <label class="mb-2">New Vehicle Inquiry</label>
                        <input class="form-control" type="text" maxlength="30"
                            wire:model="form.new_vehicle_inquiry">
                    </div>
                    <div class="col-sm-4 mb-3">
                        <label class="mb-2">Vehicle Shelf Life</label>
                        <input class="form-control" type="text" maxlength="20"
                            wire:model="form.vehicle_shelf_life">
                    </div>
                    <div class="col-sm-4 mb-3">
                        <label class="mb-2">Payment terms of sales</label>
                        <select class="form-select text-truncate" wire:model="form.payment_term_of_sales">
                            <option value="">-Select-</option>
                            <option value="Cash">Cash</option>
                            <option value="Credit - 30days">Credit - 30days </option>
                            <option value="Credit - 60days">Credit - 60days </option>
                            <option value="Credit - 90days">Credit - 90days </option>
                            <option value="LC at sight">LC at sight </option>
                            <option value="LC - 30days">LC - 30days </option>
                            <option value="LC - 60days">LC - 60days </option>
                            <option value="LC - 90days">LC - 90days </option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="row" id="leadCreateModal">
                    <form class="row g-3 needs-validation" novalidate>
                        <div class="col-sm-12 col-lg-12 col-xxl-12 mt-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-3">Initiate Sales Lead</h6>
                                <button type="submit" class="btn btn-success mb-3"
                                wire:click="saveInitiateSalesLead">Initiate</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table w-100">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Progress</th>
                                            <th style="width:20%;" class="text-center">Model</th>
                                            <th style="width:10%;">QTY</th>
                                            <th>Sales Month</th>
                                            <th>Comment</th>
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
                                                <td class="text-center">
                                                    @php
                                                        $percent = @$lastFollowup['value']?:0;
                                                    @endphp
                                                        <div role="progressbar" aria-valuenow="{{$percent}}" aria-valuemin="0" aria-valuemax="100" style="--value:{{$percent}}; margin-left: 20px"></div>
                                                </td>
                                                <td>
                                                    @if ($key !== 0)
                                                        <input type="text" class="form-control"
                                                            placeholder="Model" maxlength="20"
                                                            wire:model="fields.{{ $index }}.model"
                                                            disabled
                                                            required autocomplete="off">
                                                    @else
                                                        <x-select2-tag class="form-control" name="fields.{{ $index }}.model" id="fields.{{ $index }}.model" parentId="leadCreateModal">
                                                            <option value="">Search...</option>
                                                            @foreach ($salesLeadModels as $data)
                                                                <option value="{{ $data->name }}">{{ $data->name }}</option>
                                                            @endforeach
                                                        </x-select2-tag>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control"
                                                        placeholder="QTY" maxlength="20"
                                                        wire:model="fields.{{ $index }}.qty"
                                                        style="width: 10ch;"
                                                        @if ($key !== 0) disabled @endif
                                                        required>
                                                </td>
                                                <td>
                                                    @php
                                                    $cuurentMonth = date('m');
                                                        $currentDate = new DateTime();
                                                        for ($i = 0; $i < 4; $i++) {
                                                            $currentDate->modify('+1 month'); // Move to the next month
                                                        }
                                                        $lastMonth = $currentDate->format('n'); // Get the last month number
                                                    @endphp
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
                    </form>
                </div>

                <hr class="my-4">

                <div class="row align-items-center mb-3">
                    <div class="col-sm-12 d-sm-flex align-content-center gap-4">
                        <h6 class="mb-4">Add Images</h6>
                        <div>
                            <div class="upload-btn position-relative">
                                <label for="upload-img" type="button"
                                    class="btn btn-sm btn-dark text-white ms-md-2">📸 &nbsp;
                                    Upload/Capture Image</label>
                                    <span wire:loading wire:target="images">Uploading...</span>
                                <input type="file" class="form-control" name="images[]" multiple id="upload-img"
                                    wire:model.live="images" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="img-thumbs {{ !empty($fileNames) ? '' : 'd-none' }}" id="img-preview">
                            @foreach ($fileNames as $key => $name)
                                <div class="wrapper-thumb" bis_skin_checked="1">
                                    <a href="{{ asset('storage/images/' . $name) }}" target="_blank">
                                        <img src="{{ asset('storage/images/' . $name) }}" class="img-preview-thumb">
                                    </a>
                                    <span class="remove-btn" wire:click="removeImage({{ $key }})">x</span>
                                </div>
                            @endforeach
                            <div class="spinner-border" role="status" wire:loading wire:target="images">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        @error('images')
                            <span class="text-danger">The image size should not exceed 1MB.</span>
                        @enderror
                    </div>
                </div>
                <hr class="my-4">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h6 class="mb-3">Customer Own Workshop </h6>
                    </div>

                    <div class="col-sm-4 form-group">
                        <label>No. of WS</label>
                        <input type="number" class="form-control" min="0" maxlength="10"
                            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                            wire:model="form.custownws_no_of_ws">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>No. of Tech</label>
                        <input type="number" class="form-control" maxlength="10"
                            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                            wire:model="form.custownws_no_of_tech">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Tech Languages </label>
                        <input type="text" onkeydown="return /[a-z]/i.test(event.key)" class="form-control"
                            maxlength="20" wire:model="form.custownws_tech_languages">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Oil Used</label>
                        <input type="text" class="form-control" maxlength="10"
                            wire:model="form.custownws_oil_used">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label class="d-block">Parts</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="parts1" value="Genuine"
                                wire:model="form.custownws_parts_genuine">
                            <label class="form-check-label mb-0" for="parts1">Genuine</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="parts2" value="Non-Genuine"
                                wire:model="form.custownws_parts_non_genunine">
                            <label class="form-check-label mb-0" for="parts2">Non-Genuine</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="parts3" value="Mix"
                                wire:model="form.custownws_parts_mix">
                            <label class="form-check-label mb-0" for="parts3">Mix</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="parts3" value="Gray"
                                wire:model="form.custownws_parts_gray">
                            <label class="form-check-label mb-0" for="parts3">Gray</label>
                        </div>

                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Parts Source</label>
                        <input type="text" class="form-control" maxlength="50"
                            wire:model="form.custownws_parts_source">
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h6 class="my-3">Local Workshop </h6>
                    </div>

                    <div class="col-sm-3 form-group">
                        <label>No. of WS</label>
                        <input type="text" class="form-control" maxlength="20" wire:model="form.locws_noof_ws">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>Name of WS</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.locws_name_of_ws">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>Approx Cost</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.locws_approx_price">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>parts Utilized</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.locws_parts_utilized">
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h6 class="my-3">HINO Dealer</h6>
                    </div>

                    <div class="col-sm-6 form-group">
                        <label>City</label>
                        <input type="text" class="form-control" maxlength="20" wire:model="form.hinod_city">
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>AMC Level</label>
                        <input type="text" class="form-control" maxlength="20" wire:model="form.hinod_amc_lvl">
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h6 class="my-3">Last 12 months Transactions</h6>
                    </div>

                    <div class="col-sm-4 form-group">
                        <label>Parts 1<sup>st</sup> half</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.l12m_parts_1half">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Parts 2<sup>nd</sup> half</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.l12m_parts_2half">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Last invoice date</label>
                        <input type="date" class="form-control" maxlength="20"
                            wire:model="form.l12m_parts_date">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Service 1<sup>st</sup> half</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.l12m_service_1half">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Service 2<sup>nd</sup> half</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.l12m_service_2half">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Last invoice Date</label>
                        <input type="date" class="form-control" maxlength="20"
                            wire:model="form.l12m_service_date">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Sales 1<sup>st</sup> half</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.l12m_sales_1half">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Sales 2<sup>nd</sup> half</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.l12m_sales_2half">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Last invoice Date</label>
                        <input type="date" class="form-control" maxlength="20"
                            wire:model="form.l12m_sales_date">
                    </div>
                </div>
                <hr class="my-4">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Department</label>
                            <select class="form-select" wire:model="form.dept_id">
                                @foreach ($this->departmentData as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" x-data="{ expanded: false }" @mouseover.away = "expanded = false">
                            <label>Customer Satisfaction Scale </label>
                            <div class="custom-dropdown w-100" x-on:click="expanded = ! expanded">
                                @if ($customerStf == 5)
                                    <div class="selected-option form-control form-select"><img
                                                src="{{ asset('assets/images/Rating-5.png') }}" /> Excellent</div>
                                @elseif ($customerStf == 4)
                                    <div class="selected-option form-control form-select"><img
                                            src="{{ asset('assets/images/Rating-4.png') }}" /> Average</div>
                                @elseif ($customerStf == 3)
                                    <div class="selected-option form-control form-select"><img
                                            src="{{ asset('assets/images/Rating-3.png') }}" /> Normal</div>
                                @elseif ($customerStf == 2)
                                    <div class="selected-option form-control form-select"><img
                                            src="{{ asset('assets/images/Rating-2.png') }}" /> Poor</div>
                                @elseif ($customerStf == 1)
                                    <div class="selected-option form-control form-select"><img
                                            src="{{ asset('assets/images/Rating-1.png') }}" /> Very Poor</div>
                                @elseif ($customerStf == 6)
                                    <div class="selected-option form-control form-select"><img
                                            src="{{ asset('assets/images/Rating-6.png') }}" /> Failed</div>
                                @else
                                    <div class="selected-option form-control form-select">-Select-</div>
                                @endif
                                <ul class="dropdown-options w-100" :class="expanded ? '' : 'd-none'">
                                    <li @click="$dispatch('custom-dropdown-selected', 5)"
                                    class="{{ $customerStf == 5 ? 'selected-option' : '' }}"><img
                                    src="{{ asset('assets/images/Rating-5.png') }}" /> Excellent</li>
                                    <li @click="$dispatch('custom-dropdown-selected', 4)"
                                    class="{{ $customerStf == 4 ? 'selected-option' : '' }}"><img
                                    src="{{ asset('assets/images/Rating-4.png') }}" /> Average</li>
                                    <li @click="$dispatch('custom-dropdown-selected', 3)"
                                    class="{{ $customerStf == 3 ? 'selected-option' : '' }}"><img
                                    src="{{ asset('assets/images/Rating-3.png') }}" /> Normal</li>
                                    <li @click="$dispatch('custom-dropdown-selected', 2)"
                                    class="{{ $customerStf == 2 ? 'selected-option' : '' }}"><img
                                    src="{{ asset('assets/images/Rating-2.png') }}" /> Poor</li>
                                    <li @click="$dispatch('custom-dropdown-selected', 1)"
                                    class="{{ $customerStf == 1 ? 'selected-option' : '' }}"><img
                                    src="{{ asset('assets/images/Rating-1.png') }}" /> Very Poor</li>
                                    <li @click="$dispatch('custom-dropdown-selected', 6)"
                                    class="{{ $customerStf == 6 ? 'selected-option' : '' }}"><img
                                        src="{{ asset('assets/images/Rating-6.png') }}" /> Failed</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <label>Cutomer Voice/Comments</label>
                            <button class="btn btn-danger float-end mb-2" @click="$refs.textarea.value = ''; $wire.set('form.customer_voice', '')">Clear</button>
                            <textarea class="form-control" rows="4" maxlength="255" x-ref="textarea" placeholder="" wire:model="form.customer_voice"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 form-group">
                        <label>Customer Representative</label>
                        <input type="text" class="form-control" maxlength="30"
                            wire:model="form.aftersales_contact_person">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" maxlength="30" wire:model="form.title">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>Mobile</label>
                        <input type="number" class="form-control" maxlength="10"
                            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                            wire:model="form.mobile">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>Visit Date</label>
                        <input type="date" class="form-control" id="visit_date" wire:model="event.visit_date">
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>Visit Time</label>
                        <input type="time" class="form-control" wire:model="event.visit_time">
                    </div>
                    <div class="col-sm-12" bis_skin_checked="1">
                        <hr class="my-4">
                    </div>
                    <div class="col-sm-12 form-group">
                        <h6 class="my-3">Action Recomended by TJT</h6>

                    </div>
                    <div class="col-sm-12 col-md-4 form-group" bis_skin_checked="1">
                        <label>Sales</label>
                        <textarea class="form-control" rows="3" placeholder="Note" wire:model="form.sales_note"></textarea>
                        <div class="d-flex align-items-center gap-2" bis_skin_checked="1">
                            <select class="form-select mt-3 text-truncate" wire:model="salesUserId">
                                <option value="" selected="">-Select Person-</option>
                                <option value="0">All</option>
                                @foreach ($salesUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <a type="button" class="btn btn-sm btn-info text-white mt-3"
                                wire:click="assignSales">Assign</a>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4 form-group" bis_skin_checked="1">
                        <label>Spare parts</label>
                        <textarea class="form-control" rows="3" placeholder="Note" wire:model="form.spare_note"></textarea>
                        <div class="d-flex align-items-center gap-2" bis_skin_checked="1">
                            <select class="form-select mt-3 text-truncate" wire:model="sparePartsUserId">
                                <option value="" selected="">-Select Person-</option>
                                <option value="0">All</option>
                                @foreach ($sparePartsUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <a type="button" class="btn btn-sm btn-info text-white mt-3"
                                wire:click="assignSpareParts">Assign</a>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4 form-group" bis_skin_checked="1">
                        <label>Service</label>
                        <textarea class="form-control" rows="3" placeholder="Note" wire:model="form.service_note"></textarea>
                        <div class="d-flex align-items-center gap-2" bis_skin_checked="1">
                            <select class="form-select mt-3 text-truncate" wire:model="serviceUserId">
                                <option value="" selected="">-Select Person-</option>
                                <option value="0">All</option>
                                @foreach ($serviceUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <a type="button" class="btn btn-sm btn-info text-white mt-3"
                                wire:click="assignService">Assign</a>
                        </div>
                    </div>
                </div>


                <div class="form-group my-4 form-btn-group text-center">
                    @if (empty($id))
                        <button type="button" class="btn btn-light" wire:click="clear">Clear</button>
                        <button type="submit" name="save" class="btn btn-success"
                            wire:loading.attr="disabled">Save</button>
                    @else
                        <button type="button" class="btn btn-success" wire:click="updateRecord"
                            wire:loading.attr="disabled">Update</button>
                        <button type="button" class="btn btn-danger d-none"
                            @click="$dispatch('delete-record', { id: '{{ $id }}' })"
                            {{ !empty($id) ? '' : 'disabled' }}>Delete</button>
                    @endif

                </div>
            </form>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="scannerModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: none; background: transparent;">
                <div class="modal-body">
                    <button type="button" class="btn-close text-light" id="myModalElClose"></button>
                    <div class="scanner-control" style="position: absolute; z-index: 1;">
                        <button class="btn text-light" id="flip-button"><i
                                class="fa-solid fa-camera-rotate"></i></button>
                        <label for="qrUpload" class="btn text-light"><i class="fa-solid fa-image"></i></label>
                        <input type="file" id="qrUpload" class="d-none" />
                    </div>
                    <div id="popup-reader" bis_skin_checked="1"
                        style="position: relative; padding: 0px; border: 1px solid silver;">
                        <div style="text-align: left; margin: 0px;" bis_skin_checked="1">
                            <div bis_skin_checked="1"
                                style="position: absolute; top: 10px; right: 10px; z-index: 2; display: none; padding: 5pt; border: 1px solid silver; font-size: 10pt; background: rgb(248, 248, 248);">
                                Built using <a href="https://github.com/mebjas/html5-qrcode"
                                    target="new">html5-qrcode</a>
                                <br>
                                <br>
                                <a href="https://github.com/mebjas/html5-qrcode/issues" target="new">Report
                                    issues</a>
                            </div>
                            <div id="popup-reader__header_message" bis_skin_checked="1"
                                style="display: none; text-align: center; font-size: 14px; padding: 2px 10px; margin: 4px; border-top: 1px solid rgb(246, 246, 246); background: rgba(0, 0, 0, 0); color: rgb(17, 17, 17);">
                                Requesting camera permissions...</div>
                        </div>
                        <div id="popup-reader__scan_region"
                            style="width: 100%; min-height: 100px; text-align: center; position: relative;"
                            bis_skin_checked="1">
                            <video muted="true" playsinline="" style="width: 100%; height: 345px;"
                                id="scannerVideo"></video>
                            <canvas id="qr-canvas" width="250" height="250"
                                style="width: 250px; height: 250px; display: none;"></canvas>
                            <div id="qr-shaded-region" bis_skin_checked="1"
                                style="position: absolute; border-width: 49px 107px; border-style: solid; border-color: rgba(0, 0, 0, 0.48); box-sizing: border-box; inset: 0px;">
                                <div bis_skin_checked="1"
                                    style="position: absolute; background-color: rgb(255, 255, 255); width: 40px; height: 5px; top: -5px; left: 0px;">
                                </div>
                                <div bis_skin_checked="1"
                                    style="position: absolute; background-color: rgb(255, 255, 255); width: 40px; height: 5px; top: -5px; right: 0px;">
                                </div>
                                <div bis_skin_checked="1"
                                    style="position: absolute; background-color: rgb(255, 255, 255); width: 40px; height: 5px; top: 255px; left: 0px;">
                                </div>
                                <div bis_skin_checked="1"
                                    style="position: absolute; background-color: rgb(255, 255, 255); width: 40px; height: 5px; top: 255px; right: 0px;">
                                </div>
                                <div bis_skin_checked="1"
                                    style="position: absolute; background-color: rgb(255, 255, 255); width: 5px; height: 45px; top: -5px; left: -5px;">
                                </div>
                                <div bis_skin_checked="1"
                                    style="position: absolute; background-color: rgb(255, 255, 255); width: 5px; height: 45px; top: 215px; left: -5px;">
                                </div>
                                <div bis_skin_checked="1"
                                    style="position: absolute; background-color: rgb(255, 255, 255); width: 5px; height: 45px; top: -5px; right: -5px;">
                                </div>
                                <div bis_skin_checked="1"
                                    style="position: absolute; background-color: rgb(255, 255, 255); width: 5px; height: 45px; top: 215px; right: -5px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Book Event Modal -->
    <div class="modal fade" id="appointmentModal" aria-labelledby="appointmentModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="appointmentModalLabel">Book Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @php
                        // dd($companyid);
                    @endphp
                    <form>
                        <div class="row">
                            <div class="col-sm-12 form-group">
                                <label for="company_id">Company</label>
                                <select class="form-select" disabled id="company_id" wire:model="companyid">
                                    <option value="">Select Company</option>
                                    @foreach ($this->companyDropDown as $data)
                                        <option value="{{ $data->id }}">{{ $data->company_name }} -
                                            {{ $data->customer_code }}</option>
                                    @endforeach
                                </select>
                                @error('companyid')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-12 form-group">
                                <label for="visit_date">Visit Date</label>
                                <input type="date" id="visit_date" class="form-control" wire:model="visit_date" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                @error('visit_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
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
                        <button type="button" wire:click="createAppointment" class="btn btn-primary">Save changes</button>
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
        $wire.on('table-input', (event) => {
            setTimeout(function() {
                var inputWidth = 12;
                document.querySelectorAll('table.custom-input-width tr td input').forEach(input => {
                    inputWidth = (input.value.length > 7) ? input.value.length : inputWidth;
                    // input.addEventListener('input', () => {
                    //     input.style.width = (input.value.length > 7) ? input.value.length + 'ch' : '7ch';
                    // });
                });
                $('table.custom-input-width tr td input').css('width', inputWidth + 'ch');
            }, 500);
        });

        $wire.on('alert-update', (event) => {
            Swal.fire({
                icon: "warning",
                title: "Please click on assign button to create a ticket",
                // showCancelButton: true,
                confirmButtonColor: "#212529",
                // cancelButtonColor: "#3dd5f3",
                // cancelButtonText: "Back",
                confirmButtonText: "Back"
            }).then((result) => {
                // if (!result.isConfirmed) {
                //     $wire.dispatch('update');
                // }
            });
        });

        $wire.on('add-location', (event) => {
            // Get location cordinates
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition((position) => {
                    $coordinates = position.coords.latitude.toFixed(6) + ', ' + position.coords.longitude
                        .toFixed(6);
                    $wire.dispatch('update-location', [$coordinates]);
                }, (error) => {
                    if (error.PERMISSION_DENIED) {
                        Toast.fire({
                            icon: 'error',
                            title: 'The user has denied permission to access their location'
                        });
                    }
                }, {
                    enableHighAccuracy: true
                });
            } else {
                Toast.fire({
                    icon: 'error',
                    title: "Geolocation is not supported by this browser."
                });
            }
        });

        $wire.on('custom-dropdown-selected', (event) => {
            $wire.set('customerStf', event);
        });

        $wire.on('select-coordinate', (event) => {
            $wire.set('selectedCoordinate', event);
            $wire.dispatch('update-address');
        });

        $wire.on('showBookEventModal', (event) => {
            const companyId = event[0].companyId;
            $('#company_id').val(companyId);
            $('#company_id').attr('disabled',true);
            $('#appointmentModal').modal('show');
        });

        $wire.on('remove-location', (event) => {
            var x = document.getElementById("selectNow");
            if (x.selectedIndex > 0) {
                x.remove(x.selectedIndex);
                console.log('index',x.selectedIndex);
                $wire.dispatch('removelocation', [x.selectedIndex])
            } else {
                // Handle the case where no option is selected
                // You can also dispatch an event or perform some other action here
            }
        });
        </script>
@endscript
@push('scripts')
<script>

        // Get today's date
        var today = new Date();

        // Calculate tomorrow's date
        var tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);

        // Calculate last week's date
        var lastWeek = new Date(today);
        lastWeek.setDate(lastWeek.getDate() - 7);

        // Format dates to YYYY-MM-DD
        function formatDate(date) {
            var day = ("0" + date.getDate()).slice(-2);
            var month = ("0" + (date.getMonth() + 1)).slice(-2);
            var year = date.getFullYear();
            return year + "-" + month + "-" + day;
        }

        // Set the min and max attributes
        document.getElementById("visit_date").setAttribute("min", formatDate(lastWeek));
        document.getElementById("visit_date").setAttribute("max", formatDate(tomorrow));
        //document.addEventListener('livewire:init', () => {
        var front = false;
        var myInterval;
        var stream = null;
        var video = document.getElementById('scannerVideo');
        var myModalEl = new bootstrap.Modal(document.getElementById('scannerModal'), {
            keyboard: false
        });
        var myModalElOpen = document.getElementById('myModalElShow');
        var myModalElClose = document.getElementById('myModalElClose');

        document.getElementById("flip-button").onclick = () => {
            front = !front;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            startCamera(front);
        };

        myModalElOpen.addEventListener('click', function() {
            myModalEl.show();
            video.removeAttribute('src');
            startCamera();
        });
        myModalElClose.addEventListener('click', function(event) {
            myModalEl.hide();
        });

        document.getElementById('scannerModal').addEventListener('hidden.bs.modal', function(event) {
            stopCamera();
        })

        function startCamera(front) {
            console.log('scanner start.....');
            navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: front ? "user" : "environment"
                    }
                })
                .then(newStream => {
                    stream = newStream;
                    if (video) {
                        video.srcObject = newStream;
                        video.play();
                    }
                    const canvasElement = document.getElementById('qr-canvas');
                    const canvas = canvasElement.getContext('2d');
                    qrCodeFinder(canvasElement, canvas, video);
                })
                .catch(error => {
                    console.error('Error accessing camera:', error);
                });
        }

        function qrCodeFinder(canvasElement, canvas, video) {
            canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
            const imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement
                .height);
            // console.log(imageData);
            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: 'dontInvert',
            });
            if (code) {
                console.log(canvasElement.toDataURL());
                myModalEl.hide();
                console.log('Found QR code:', code);
                Toast.fire({
                    icon: 'success',
                    title: "QR code scan successfully!"
                });
                Livewire.dispatch('qrScanned', {
                    data: code.data,
                    image: canvasElement.toDataURL()
                });
            } else if (stream) {
                setTimeout(() => {
                    console.log('re-trying.......');
                    qrCodeFinder(canvasElement, canvas, video);
                }, 100);
            }
        }

        function stopCamera() {
            clearInterval(myInterval);
            console.log('scanner stop.....');
            if (video) {
                video.pause();
                video.removeAttribute('src');
            }
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        function triggerBookEvent(companyId) {
            Livewire.dispatch('triggerBookEvent',{
                companyId: companyId
                });
        }

        //});
        setTimeout(function() {
            var inputWidth = 12;
            document.querySelectorAll('table.custom-input-width tr td input').forEach(input => {
                inputWidth = (input.value.length > 7) ? input.value.length : inputWidth;
                // input.addEventListener('input', () => {
                //     input.style.width = (input.value.length > 7) ? input.value.length + 'ch' : '7ch';
                // });
            });
            $('table.custom-input-width tr td input').css('width', inputWidth + 'ch');
        }, 500);
    </script>
@endpush
