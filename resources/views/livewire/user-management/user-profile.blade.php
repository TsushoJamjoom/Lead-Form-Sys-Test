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
                <a class="btn btn-outline-dark  me-0" href="{{ App\Helpers\AppHelper::getPreviousUrl('user-list') }}"
                    wire:navigate>Back</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form class="mt-2 needs-validation {{ $errors->any() ? 'was-validated' : '' }}" wire:submit="update"
                novalidate>
                <div class="row mb-4">
                    <div class="form-group col-md-4">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" disabled class="form-control" id="name" wire:model='name' required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" disabled class="form-control"id="email" wire:model='email' required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label for="department_id" class="form-label">Department</label>
                        <select class="form-select" disabled wire:model="department_id" required>
                            <option value="">Select Department</option>
                            @foreach ($this->departmentList as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6 col-xl-3">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select class="form-select" disabled wire:model="branch_id" required>
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
                        <select class="form-select" disabled wire:model.live="role_id" required>
                            <option value="">Select Position</option>
                            @foreach ($this->positionList as $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" disabled wire:model="status" required>
                            <option value="">Select Status</option>
                            <option value="0">Inactive</option>
                            <option value="1">Active</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label for="profile_photo" class="form-label">Profile Photo</label>
                        <input type="file" class="form-control" id="profile_photo" wire:model="profile_photo" accept="image/*">
                        @error('profile_photo')
                            <span class="text-danger">{{ $message }}</span><br>
                        @enderror
                        @if (!blank($this->profile_photo) && in_array($this->profile_photo->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']))
                        <span> Image Preview:</span>
                            <div class="mt-2">
                                <img src="{{ $profile_photo->temporaryUrl() }}" alt="Preview" class="img-thumbnail" style="width: 150px; height: 150px;">
                            </div>
                        @elseif(@$profile_photo)
                            <span class="text-danger">Selected file is not an image.</span>
                        @elseif(@$profile_photo_url)
                        <span class="mt-4"> Image Preview:</span>
                            <div class="mt-2">
                                <img src="{{ $profile_photo_url }}" alt="Preview" class="img-thumbnail" style="width: 150px; height: 150px;">
                            </div>
                        @endif
                        
                        
                    </div>
                </div>
                <div class="text-center mt-5 form-btn-group">
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form><!-- End Multi Columns Form -->

        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header">
            <h4>Change Password</h4>
        </div>
        <form wire:submit="updatePassword">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <label for="current_password">Current Password</label>
                        <div class="input-group mt-3" x-data="{ expanded: false }">
                            <input type="password" :type="!expanded ? 'password' : 'text'" class="form-control"
                                id="current_password" wire:model.live.debounce.250="current_password" />
                            <button type="button" class="input-group-text" id="basic-addon2"
                                x-on:click="expanded = ! expanded">
                                <i class="fas fa-eye" x-show="!expanded"></i>
                                <i class="fas fa-eye-slash" x-show="expanded"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label for="new_password">New Password</label>
                        <div class="input-group mt-3" x-data="{ expanded: false }">
                            <input type="password" :type="!expanded ? 'password' : 'text'" class="form-control"
                                id="new_password" wire:model.live.debounce.250="new_password" />
                            <button type="button" class="input-group-text" id="basic-addon2"
                                x-on:click="expanded = ! expanded">
                                <i class="fas fa-eye" x-show="!expanded"></i>
                                <i class="fas fa-eye-slash" x-show="expanded"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="input-group mt-3" x-data="{ expanded: false }">
                            <input type="password" :type="!expanded ? 'password' : 'text'" class="form-control"
                                id="confirm_password" wire:model.live.debounce.250="confirm_password" />
                            <button type="button" class="input-group-text" id="basic-addon2"
                                x-on:click="expanded = ! expanded">
                                <i class="fas fa-eye" x-show="!expanded"></i>
                                <i class="fas fa-eye-slash" x-show="expanded"></i>
                            </button>
                        </div>
                        @error('confirm_password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="car-row text-end mt-5 mb-2">
                    <button class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
