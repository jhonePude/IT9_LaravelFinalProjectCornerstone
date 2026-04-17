<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */public function store(\App\Http\Requests\Auth\LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    // CUSTOM: Check if account is inactive
    if (Auth::user()->Account_Status === 'Inactive') {
        Auth::logout();
        return back()->withErrors(['error' => 'Your account is currently deactivated.']);
    }

    $request->session()->regenerate();

    // CUSTOM: Redirect based on Role
    if (Auth::user()->Role_Id == 1) {
        return redirect()->intended('/dashboard');
    } 
    
    return redirect()->intended('/member/portal');
}
}
