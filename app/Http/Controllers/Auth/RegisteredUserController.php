<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse {
        $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'unique:user,Username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:user,Email'],
            'password' => ['required', 'min:7'],
        ]);

        $profile = ($request->gender == 'Male') ? 'profile-male.png' : 
                   (($request->gender == 'Female') ? 'profile-female.png' : 'profile-others.jpg');

        $user = User::create([
            'Fullname' => $request->fullname,
            'Username' => $request->username,
            'Email'    => $request->email,
            'Password' => Hash::make($request->password),
            'Gender'   => $request->gender,
            'Contact_Number' => $request->contact,
            'Profile_Picture' => $profile,
            'Role_Id' => 2,
            'Account_Status' => 'Active'
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended('/member/portal');
    }
}