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
                <a class="btn btn-outline-dark" href="{{ App\Helpers\AppHelper::getPreviousUrl('history-list') }}" wire:navigate>Back</a>
            </div>
        </div>
    </div>

    <div class="card p-3">
        <div class="card-body">
            <form class="custom-css" wire:submit="store">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="compnay-logo">
                                <img src="{{ asset('assets/images/company-logo.png') }}" alt="logo" />
                            </div>
                            <div class="user-logo-preview position-relative">
                                @if (!empty($userLogoName))
                                    <label class="user-logo-area">
                                        <a href="{{ asset('storage/user-logo/' . $userLogoName) }}" target="_blank">
                                            <img id="LogoPreview" src="{{ asset('storage/user-logo/' . $userLogoName) }}"
                                            alt="logo" />
                                        </a>
                                    </label>
                                @else
                                    <label for="user-logo" class="user-logo-area">
                                        <img id="LogoPreview" src="{{ asset('assets/images/dummy-logo.webp') }}"
                                            alt="logo"/>
                                    </label>
                                @endif
                            </div>

                            <div class="d-block d-sm-flex align-items-center gap-2 search-box">
                                <div class="input-group mb-3 mb-sm-0 ">
                                    <input type="text" class="form-control" placeholder="Search customer code/name"
                                        wire:model="searchVal" disabled>
                                    <span class="input-group-text" id="btn-search" wire:click="search"><i
                                            class="fa-solid fa-search"></i></span>
                                </div>
                                <button type="button" class="btn btn-warning text-nowrap" disabled>Scan QR
                                    Code</button>
                                <button type="button" class="btn btn-dark" onclick="window.print()">Print</button>
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
                                <input type="text" class="form-control" maxlength="100"
                                    wire:model="form.company_name" disabled>
                                @error('form.company_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-3 form-group">
                                <label>Customer code</label>
                                <input type="text" class="form-control" maxlength="20"
                                    wire:model="form.customer_code" disabled>
                                @error('form.customer_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
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
                                <input type="text" class="form-control" maxlength="20" wire:model="form.crid" disabled>
                                @error('form.crid')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>VAT</label>
                                <input type="text" class="form-control" maxlength="20" wire:model="form.vat" disabled>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>Company Website</label>
                                <input type="text" class="form-control" maxlength="255" wire:model="form.website" disabled>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label class="mb-2" for="cordinates">Coordinates</label>
                                <input class="form-control" id="cordinates" type="text" maxlength="100"
                                    wire:model="form.coordinates" disabled>
                            </div>
                            <div class="col-sm-12 form-group" bis_skin_checked="1">
                                <label>National Address</label>
                                <input type="text" class="form-control" maxlength="255"
                                    wire:model="form.national_address" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="name-l">Sales Channel contact person</label>
                            <input type="text" class="form-control" maxlength="50"
                                wire:model="form.contact_person" disabled>
                            @error('form.contact_person')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Position</label>
                            <input type="text" class="form-control" maxlength="50" wire:model="form.position" disabled>
                        </div>
                        <div class="form-group">
                            <label>Mobile No.</label>
                            <input type="number" class="form-control" maxlength="10" wire:model="form.mobile_no" disabled>
                            @error('form.mobile_no')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" maxlength="30" wire:model="form.email" disabled>
                            @error('form.email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-12 form-group align-items-center">
                        <label class="me-3 mb-0 d-block mb-2 mb-lg-0">Type of Business</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                wire:model="form.construction" disabled>
                            <label class="form-check-label" for="inlineCheckbox1">Construction</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox2"
                                wire:model="form.food" disabled>
                            <label class="form-check-label" for="inlineCheckbox2">Food</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox3"
                                wire:model="form.rental" disabled>
                            <label class="form-check-label" for="inlineCheckbox3">Rental</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="inlineCheckbox4"
                                wire:model="form.logistics" disabled>
                            <label class="form-check-label" for="inlineCheckbox4">Logistics</label>
                        </div>
                        <div
                            class="ps-0 ps-lg-3 me-0 form-check form-check-inline d-inline-flex align-items-center gap-2">
                            <label class="form-check-label mb-0 text-nowrap">Describe other</label>
                            <input class="form-control" type="text" maxlength="100"
                                wire:model="form.describe_other" disabled>
                        </div>
                    </div>
                </div>
                <hr class="my-4">

                <div class="row">
                    <div class="col-sm-12 col-lg-8 col-xxl-9">
                        <div class="table-responsive history-view-table">
                            <table class="table w-100">
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
                                        <th>EUROPEAN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Pick-up Truck</td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.hino_pick_up" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.isuzu_pick_up" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.fuso_pick_up" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sitrak_pick_up" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sany_pick_up" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.shacman_pick_up" disabled></td>
                                        <td><input type="number" class="form-control" wire:model="form.faw_pick_up" disabled>
                                        </td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sinotruk_pick_up" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.european_pick_up" disabled></td>
                                    </tr>
                                    <tr>
                                        <td>Light Duty Truck </td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.hino_light_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.isuzu_light_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.fuso_light_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sitrak_light_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sany_light_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.shacman_light_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.faw_light_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sinotruk_light_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.european_light_duty_truck" disabled></td>
                                    </tr>
                                    <tr>
                                        <td>Medium Duty Truck</td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.hino_medium_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.isuzu_medium_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.fuso_medium_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sitrak_medium_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sany_medium_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.shacman_medium_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.faw_medium_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sinotruk_medium_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.european_medium_duty_truck" disabled></td>
                                    </tr>
                                    <tr>
                                        <td>Heavy Duty Truck </td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.hino_heavy_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.isuzu_heavy_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.fuso_heavy_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sitrak_heavy_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sany_heavy_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.shacman_heavy_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.faw_heavy_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sinotruk_heavy_duty_truck" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.european_heavy_duty_truck" disabled></td>
                                    </tr>
                                    <tr class="bg-light-gray">
                                        <td><strong>Total</strong></td>
                                        <td><input type="number" class="form-control" wire:model="form.hino_total" disabled>
                                        </td>
                                        <td><input type="number" class="form-control" wire:model="form.isuzu_total" disabled>
                                        </td>
                                        <td><input type="number" class="form-control" wire:model="form.fuso_total" disabled>
                                        </td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sitrak_total" disabled></td>
                                        <td><input type="number" class="form-control" wire:model="form.sany_total" disabled>
                                        </td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.shacman_total" disabled></td>
                                        <td><input type="number" class="form-control" wire:model="form.faw_total" disabled>
                                        </td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.sinotruk_total" disabled></td>
                                        <td><input type="number" class="form-control"
                                                wire:model="form.european_total" disabled></td>
                                    </tr>
                                    <tr>
                                        <td>Oldest Modal Year Range</td>
                                        <td><input type="text" class="form-control" maxlength="30"
                                                wire:model="form.hino_oldest" disabled>
                                        </td>
                                        <td><input type="text" class="form-control"
                                                wire:model="form.isuzu_oldest" disabled></td>
                                        <td><input type="text" class="form-control" maxlength="30"
                                                wire:model="form.fuso_oldest" disabled>
                                        </td>
                                        <td><input type="text" class="form-control"
                                                wire:model="form.sitrak_oldest" disabled></td>
                                        <td><input type="text" class="form-control" maxlength="30"
                                                wire:model="form.sany_oldest" disabled>
                                        </td>
                                        <td><input type="text" class="form-control"
                                                wire:model="form.shacman_oldest" disabled></td>
                                        <td><input type="text" class="form-control" maxlength="30"
                                                wire:model="form.faw_oldest" disabled>
                                        </td>
                                        <td><input type="text" class="form-control" maxlength="30"
                                                wire:model="form.sinotruk_oldest" disabled></td>
                                        <td><input type="text" class="form-control" maxlength="30"
                                                wire:model="form.european_oldest" disabled></td>
                                    </tr>
                                    <tr>
                                        <td>Latest Modal Year Range</td>
                                        <td><input type="text" class="form-control" maxlength="30"
                                                wire:model="form.hino_latest" disabled>
                                        </td>
                                        <td><input type="text" class="form-control"
                                                wire:model="form.isuzu_latest" disabled></td>
                                        <td><input type="text" class="form-control" maxlength="30"
                                                wire:model="form.fuso_latest" disabled>
                                        </td>
                                        <td><input type="text" class="form-control"
                                                wire:model="form.sitrak_latest" disabled></td>
                                        <td><input type="text" class="form-control" maxlength="30"
                                                wire:model="form.sany_latest" disabled>
                                        </td>
                                        <td><input type="text" class="form-control"
                                                wire:model="form.shacman_latest" disabled></td>
                                        <td><input type="text" class="form-control" maxlength="30"
                                                wire:model="form.faw_latest" disabled>
                                        </td>
                                        <td><input type="text" class="form-control" maxlength="30"
                                                wire:model="form.sinotruk_latest" disabled></td>
                                        <td><input type="text" class="form-control" maxlength="30"
                                                wire:model="form.european_latest" disabled></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="mb-2">New Vehide Inquiry</label>
                                <input class="form-control" type="text" maxlength="100"
                                    wire:model="form.new_vehicle_inquiry" disabled>
                            </div>
                            <div class="col-sm-6 mt-3 mt-sm-0">
                                <label class="mb-2">Vehide Shelf Life</label>
                                <input class="form-control" type="text" maxlength="20"
                                    wire:model="form.vehicle_shelf_life" disabled>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-12 col-lg-4 col-xxl-3">
                        <div class="mb-3 mt-3 mt-lg-0">
                            <h6>Cities of operation</h6>
                        </div>
                        <div class="row ps-3 citeis-list">
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox1" value="Jeddah"
                                    wire:model="form.jeddah" disabled>
                                <label class="form-check-label" for="Checkbox1">Jeddah</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox2" value="Makkah"
                                    wire:model="form.makkah" disabled>
                                <label class="form-check-label" for="Checkbox2">Makkah</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox3" value="Najran"
                                    wire:model="form.najran" disabled>
                                <label class="form-check-label" for="Checkbox3">Najran</label>
                            </div>

                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox4" value="Madina"
                                    wire:model="form.madina" disabled>
                                <label class="form-check-label" for="Checkbox4">Madina</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox5" value="Alyth"
                                    wire:model="form.alyth" disabled>
                                <label class="form-check-label" for="Checkbox5">Alyth</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox6" value="Jizan"
                                    wire:model="form.jizan" disabled>
                                <label class="form-check-label" for="Checkbox6">Jizan</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox7" value="Riyadh"
                                    wire:model="form.riyadh" disabled>
                                <label class="form-check-label" for="Checkbox7">Riyadh</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox8" value="Yanbu"
                                    wire:model="form.yanbu" disabled>
                                <label class="form-check-label" for="Checkbox8">Yanbu</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox9" value="Khamis"
                                    wire:model="form.khamis" disabled>
                                <label class="form-check-label" for="Checkbox9">Khamis</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox10" value="Dammam"
                                    wire:model="form.dammam" disabled>
                                <label class="form-check-label" for="Checkbox10">Dammam</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox11" value="Buraidah"
                                    wire:model="form.buraidah" disabled>
                                <label class="form-check-label" for="Checkbox11">Buraidah</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox12" value="Tabuk"
                                    wire:model="form.tabuk" disabled>
                                <label class="form-check-label" for="Checkbox12">Tabuk</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox13" value="Al-khobar"
                                    wire:model="form.al_khobar" disabled>
                                <label class="form-check-label" for="Checkbox13">Al-khobar</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox14" value="Hail"
                                    wire:model="form.hail" disabled>
                                <label class="form-check-label" for="Checkbox14">Hail</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox15" value="Taif"
                                    wire:model="form.taif" disabled>
                                <label class="form-check-label" for="Checkbox15">Taif</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox16" value="Abha"
                                    wire:model="form.abha" disabled>
                                <label class="form-check-label" for="Checkbox16">Abha</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox17" value="Al-Baha"
                                    wire:model="form.al_baha" disabled>
                                <label class="form-check-label" for="Checkbox17">Al-Baha</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox18" value="Neom"
                                    wire:model="form.neom" disabled>
                                <label class="form-check-label" for="Checkbox18">Neom</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox19" value="Hafr Batin"
                                    wire:model="form.hafr_batin" disabled>
                                <label class="form-check-label" for="Checkbox19">Hafr Batin</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox20" value="Alqassim"
                                    wire:model="form.alqassim" disabled>
                                <label class="form-check-label" for="Checkbox20">Alqassim</label>
                            </div>
                            <div class="col-6 col-sm-4 form-check ">
                                <input class="form-check-input" type="checkbox" id="Checkbox21" value="Jubail"
                                    wire:model="form.jubail" disabled>
                                <label class="form-check-label" for="Checkbox21">Jubail</label>
                            </div>
                            <div class="col-sm-12 ps-0">
                                <label class="mb-2">Other Cities</label>
                                <input class="form-control" type="text" maxlength="200"
                                    wire:model="form.other_cities" disabled>
                            </div>
                            <div class="col-sm-12 ps-0 mt-3">
                                <label class="mb-2">Payment terms of sales</label>
                                <select class="form-select text-truncate" wire:model="form.payment_term_of_sales" disabled>
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
                    </div>
                </div>

                <hr class="my-4">

                <div class="row align-items-center mb-3">
                    <div class="col-sm-12 d-sm-flex align-content-center gap-4">
                        <h6 class="mb-4">Add Images</h6>
                    </div>
                    <div class="col-sm-12">
                        <div class="img-thumbs {{ !empty($fileNames) ? '' : 'd-none' }}" id="img-preview">
                            @foreach ($fileNames as $key => $name)
                                <div class="wrapper-thumb" bis_skin_checked="1">
                                    <a href="{{ asset('storage/images/' . $name) }}" target="_blank">
                                        <img src="{{ asset('storage/images/' . $name) }}" class="img-preview-thumb">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h6 class="mb-3">Customer Own Workshop </h6>
                    </div>

                    <div class="col-sm-4 form-group">
                        <label>No. of WS</label>
                        <input type="number" class="form-control" wire:model="form.custownws_no_of_ws" disabled>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>No. of Tech</label>
                        <input type="number" class="form-control" wire:model="form.custownws_no_of_tech" disabled>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Tech Languages </label>
                        <input type="text" class="form-control" maxlength="100"
                            wire:model="form.custownws_tech_languages" disabled>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Oil Used</label>
                        <input type="text" class="form-control" maxlength="100"
                            wire:model="form.custownws_oil_used" disabled>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label class="d-block">Parts</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="parts1" value="Genuine"
                                wire:model="form.custownws_parts_genuine" disabled>
                            <label class="form-check-label mb-0" for="parts1">Genuine</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="parts2" value="Non-Genuine"
                                wire:model="form.custownws_parts_non_genunine" disabled>
                            <label class="form-check-label mb-0" for="parts2">Non-Genuine</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="parts3" value="Mix"
                                wire:model="form.custownws_parts_mix" disabled>
                            <label class="form-check-label mb-0" for="parts3">Mix</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="parts3" value="Gray"
                                wire:model="form.custownws_parts_gray" disabled>
                            <label class="form-check-label mb-0" for="parts3">Gray</label>
                        </div>

                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Parts Source</label>
                        <input type="text" class="form-control" maxlength="200"
                            wire:model="form.custownws_parts_source" disabled>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h6 class="my-3">Local Workshop </h6>
                    </div>

                    <div class="col-sm-3 form-group">
                        <label>No. of WS</label>
                        <input type="text" class="form-control" maxlength="20" wire:model="form.locws_noof_ws" disabled>
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>Name of WS</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.locws_name_of_ws" disabled>
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>Approx Cost</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.locws_approx_price" disabled>
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>parts Utilized</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.locws_parts_utilized" disabled>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h6 class="my-3">HINO Dealer</h6>
                    </div>

                    <div class="col-sm-6 form-group">
                        <label>City</label>
                        <input type="text" class="form-control" maxlength="20" wire:model="form.hinod_city" disabled>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label>AMC Level</label>
                        <input type="text" class="form-control" maxlength="20" wire:model="form.hinod_amc_lvl" disabled>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h6 class="my-3">Last 12 months Transactions</h6>
                    </div>

                    <div class="col-sm-4 form-group">
                        <label>Parts 1<sup>st</sup> half</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.l12m_parts_1half" disabled>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Parts 2<sup>nd</sup> half</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.l12m_parts_2half" disabled>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Last invoice date</label>
                        <input type="date" class="form-control" maxlength="20" wire:model="form.l12m_parts_date" disabled>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Service 1<sup>st</sup> half</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.l12m_service_1half" disabled>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Service 2<sup>nd</sup> half</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.l12m_service_2half" disabled>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Last invoice Date</label>
                        <input type="date" class="form-control" maxlength="20"
                            wire:model="form.l12m_service_date" disabled>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Sales 1<sup>st</sup> half</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.l12m_sales_1half" disabled>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Sales 2<sup>nd</sup> half</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="form.l12m_sales_2half" disabled>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Last invoice Date</label>
                        <input type="date" class="form-control" maxlength="20" wire:model="form.l12m_sales_date" disabled>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label>Department</label>
                            <select class="form-select" wire:model="form.dept_id" disabled>
                                <option value="">-Select-</option>
                                <option value="1">Sales</option>
                                <option value="2">Spare parts</option>
                                <option value="3">Service</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Customer Satisfaction Scale </label>
                            <div class="w-100">
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
                                @else
                                    <div class="selected-option form-control form-select">-Select-</div>
                                @endif
                                <ul class="dropdown-options d-none w-100">
                                    <li class="{{ $customerStf == 5 ? 'selected-option' : '' }}"><img
                                            src="{{ asset('assets/images/Rating-5.png') }}" /> Excellent</li>
                                    <li class="{{ $customerStf == 4 ? 'selected-option' : '' }}"><img
                                            src="{{ asset('assets/images/Rating-4.png') }}" /> Average</li>
                                    <li class="{{ $customerStf == 3 ? 'selected-option' : '' }}"><img
                                            src="{{ asset('assets/images/Rating-3.png') }}" /> Normal</li>
                                    <li class="{{ $customerStf == 2 ? 'selected-option' : '' }}"><img
                                            src="{{ asset('assets/images/Rating-2.png') }}" /> Poor</li>
                                    <li class="{{ $customerStf == 1 ? 'selected-option' : '' }}"><img
                                            src="{{ asset('assets/images/Rating-1.png') }}" /> Very Poor</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <label>Cutomer Voice/Comments</label>
                            <textarea class="form-control" rows="4" maxlength="255" placeholder="" wire:model="form.customer_voice" disabled></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 form-group">
                        <label>Customer Representative</label>
                        <input type="text" class="form-control" maxlength="100"
                            wire:model="form.aftersales_contact_person" disabled>
                    </div>
                    <div class="col-sm-3 form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" maxlength="100" wire:model="form.title" disabled>
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>Mobile</label>
                        <input type="text" class="form-control" maxlength="10" wire:model="form.mobile" disabled>
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>Visit Date</label>
                        <input type="date" class="form-control" wire:model="form.visit_date" disabled>
                    </div>
                    <div class="col-sm-2 form-group">
                        <label>Visit Time</label>
                        <input type="time" class="form-control" wire:model="form.visit_time" disabled>
                    </div>
                    <div class="col-sm-12" bis_skin_checked="1">
                        <hr class="my-4">
                    </div>
                    <div class="col-sm-12 form-group">
                        <h6 class="my-3">Action Recomended by TJT</h6>

                    </div>
                    <div class="col-sm-12 col-md-4 form-group" bis_skin_checked="1">
                        <label>Sales</label>
                        <textarea class="form-control" rows="3" placeholder="Note" wire:model="form.sales_note" disabled></textarea>
                        <div class="d-flex align-items-center gap-2" bis_skin_checked="1">
                            <select class="form-select mt-3 text-truncate" wire:model="salesUserId" disabled>
                                <option selected="">-Select Person-</option>
                                <option value="0">All</option>
                                @foreach ($salesUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-sm btn-info text-white mt-3" disabled>Assign</button>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4 form-group" bis_skin_checked="1">
                        <label>Spare parts</label>
                        <textarea class="form-control" rows="3" placeholder="Note" wire:model="form.spare_note" disabled></textarea>
                        <div class="d-flex align-items-center gap-2" bis_skin_checked="1">
                            <select class="form-select mt-3 text-truncate" wire:model="sparePartsUserId" disabled>
                                <option selected="">-Select Person-</option>
                                <option value="0">All</option>
                                @foreach ($sparePartsUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-sm btn-info text-white mt-3" disabled>Assign</button>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4 form-group" bis_skin_checked="1">
                        <label>Service</label>
                        <textarea class="form-control" rows="3" placeholder="Note" wire:model="form.service_note" disabled></textarea>
                        <div class="d-flex align-items-center gap-2" bis_skin_checked="1">
                            <select class="form-select mt-3 text-truncate" wire:model="serviceUserId" disabled>
                                <option selected="">-Select Person-</option>
                                <option value="0">All</option>
                                @foreach ($serviceUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-sm btn-info text-white mt-3" disabled>Assign</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
