<?php

namespace App\Livewire\UserManagement;

use App\Models\User;
use Livewire\Component;

class UserView extends Component
{
    public $title = 'View User';
    public $data;

    public function mount($id)
    {
        $this->data = User::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.user-management.user-view')
        ->title($this->title);
    }
}
