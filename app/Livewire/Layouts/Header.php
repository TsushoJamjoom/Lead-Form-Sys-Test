<?php

namespace App\Livewire\Layouts;

use Illuminate\Http\Request;
use Livewire\Component;

class Header extends Component
{

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.layouts.header');
    }
}
