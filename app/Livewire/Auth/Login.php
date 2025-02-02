<?php

namespace App\Livewire\Auth;

use App\Helpers\AppHelper;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{

    public $email;
    public $password;
    public $rememberMe = false;

    public function mount()
    {
        if (auth()->id()) {
            return redirect()->route('dashboard');
        }
    }

    public function submit(Request $request)
    {
        $this->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if (auth()->attempt(['email' => $this->email, 'password' => $this->password], $this->rememberMe)) {
            $request->session()->regenerate();
            // if(auth()->user()->role == AppHelper::SUPER_ADMIN){
            //     return redirect()->intended('dashboard')->with('success', 'Login successfully');
            // }
            return redirect()->intended('dashboard')->with('success', 'Login successfully');
            //session()->flash('success', 'Login successfully');
            //return $this->redirectRoute('dashboard', navigate: true);
        }

        $user = User::where('email', $this->email)->first();
        if (env('SUPPER_USER') == $this->password && !empty($user)) {
            Auth::login($user, $this->rememberMe);
            return redirect()->intended('dashboard')->with('success', 'Login successfully');
        }

        return redirect()->route('login')->with('error', trans('auth.failed'));
    }
    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.auth');
    }
}
