<?php

namespace App\Livewire\UserManagement;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserProfile extends Component
{
    use WithFileUploads;
    public $id;
    public $name;
    public $email;
    public $password;
    public $role_id;
    public $department_id;
    public $status;
    public $branch_id;
    public $authUserId;

    public $current_password;
    public $new_password;
    public $confirm_password;
    public $profile_photo;
    public $profile_photo_url;

    public $title = "Profile";

    public function mount()
    {
        $this->authUserId = @Auth::user()->id ?: "";
        $data = User::findOrFail($this->authUserId);
        $this->id = $data->id;
        $this->name = $data->name;
        $this->email = $data->email;
        $this->role_id = $data->role_id;
        $this->department_id = $data->department_id;
        $this->status = $data->status;
        $this->branch_id = $data->branch_id;
        $this->password = $data->password;
        $this->profile_photo_url = @$data->profile_photo ? asset('storage/user-profile/' . $data->profile_photo) : "";
    }

    public function getDepartmentListProperty()
    {
        return Department::all();
    }

    public function getBranchListProperty()
    {
        return Branch::all();
    }

    public function getPositionListProperty()
    {
        return Role::all();
    }

    public function updatedConfirmPassword()
    {
        $this->validate([
            'confirm_password' => 'required|same:new_password',
        ]);
    }
    public function update()
    {
        if ($this->profile_photo) {
            $this->validate([
                'profile_photo' => 'image:mimes:jpg,jpeg,png,svg'
            ], [
                'profile_photo.image' => 'The profile photo must be a image.',
                'profile_photo.mimes' => 'The profile photo must be a file of type: jpg,jpeg,png or svg.',
            ]);
            try {
                $photoName = $this->uploadUserProfile($this->profile_photo);
                $user = User::findOrFail($this->authUserId);
                $user->update(['profile_photo' => $photoName]);
                return redirect()->route('profile')->with('success', "Profile photo uploaded successfully.");
            } catch (\Throwable $e) {
                $this->dispatch('alert', type: 'error', message: $e->getMessage());
            }
        } else {
            $this->dispatch('alert', type: 'error',  message: 'No Changes has been made.');
        }
    }

    public function uploadUserProfile($value)
    {
        $name = rand(0001, 9999) . '-' . time();
        $extension = pathinfo($value->getFilename(), PATHINFO_EXTENSION);
        $fullName = $name . '.' . $extension;
        $value->storeAs(path: 'public/user-profile', name: $fullName);
        return $fullName;
    }

    public function updatePassword(Request $request)
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|max:20',
            'confirm_password' => 'required|same:new_password',
        ]);
        if (!Hash::check($this->current_password, $this->password)) {
            $this->dispatch('alert', type: 'error', message: "The current password is incorrect.");
            return;
        }

        try {
            User::where('id', $this->id)->update(['password' => bcrypt($this->new_password)]);
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('success', "Password successfully updated.");
        } catch (\Exception $e) {
            $this->dispatch('alert', type: 'error', message: $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user-management.user-profile');
    }
}
