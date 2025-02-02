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
    <div class="card p-3" id="createCompany">
        <div class="card-body">
            <form class="custom-css" wire:submit="create">
                <div class="row">
                    <div class="col-sm-12">
                        <div
                            class="d-flex justify-content-center justify-content-sm-between align-items-center flex-wrap gap-2">
                            <div class="compnay-logo">
                                <img src="{{ asset('assets/images/company-logo.png') }}" alt="logo" />
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mt-3 mb-4">
                <div class="row">
                    <div class="col-sm-6 col-md-6 col-lg-3 form-group">
                        <label for="name-f">Compnay name</label>
                        <input type="text" class="form-control" maxlength="50"
                            wire:model="company_name">
                        @error('company_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3 form-group">
                        <label>Customer code</label>
                        <input type="text" class="form-control" maxlength="20"
                            wire:model="customer_code">
                        @error('customer_code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3 form-group">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select class="form-select" wire:model="branch_id">
                            <option value="">Select Branch</option>
                            @foreach ($this->branchList as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-3 form-group">
                        <label>Sales Users</label>
                        <x-select2 class="form-control custom-select-2" name="sales_user_id" id="sales_user_id" wire:model="sales_user_id" parentId="createCompany">
                            <option value="">Select User</option>
                            <option value="0" {{ 0 === $sales_user_id ? 'selected' : '' }}>All</option>
                            @foreach ($this->salesUsers as $user)
                                <option value="{{ $user->id }}" {{ $user->id == $sales_user_id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </x-select2>
                        @error('sales_user_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
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
                    @endif

                </div>
            </form>
        </div>
    </div>
</div>
