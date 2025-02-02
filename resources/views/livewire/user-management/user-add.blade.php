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
            <a class="btn btn-outline-dark me-0" href="{{ App\Helpers\AppHelper::getPreviousUrl('user-list') }}" wire:navigate>Back</a>
         </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="mt-2" wire:submit="store"
                novalidate>
                <div class="row mb-4">
                    <div class="form-group col-md-6 col-xl-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" maxlength="20" id="name" wire:model='name' required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6 col-xl-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control"id="email" maxlength="30" wire:model='email' required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6 col-xl-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" wire:model='password' required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6 col-xl-3">
                        <label for="password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password" wire:model='confirm_password' required>
                        @error('confirm_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6 col-xl-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select class="form-select" wire:model="department_id" required>
                            <option value="">Select Department</option>
                            @foreach ($this->departmentList as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6 col-xl-3">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select class="form-select" wire:model="branch_id" required>
                            <option value="">Select Branch</option>
                            @foreach ($this->branchList as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6 col-xl-3">
                        <label for="role_id" class="form-label">Position</label>
                        <select class="form-select" wire:model.live="role_id" required>
                            <option value="">Select Position</option>
                            @foreach ($this->positionList as $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6 col-xl-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" wire:model="status" required>
                            <option value="">Select Status</option>
                            <option value="0">Inactive</option>
                            <option value="1">Active</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="text-center mt-5 form-btn-group">
                    <button type="reset" class="btn btn-light">Clear</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form><!-- End Multi Columns Form -->

        </div>
    </div>
</div>
