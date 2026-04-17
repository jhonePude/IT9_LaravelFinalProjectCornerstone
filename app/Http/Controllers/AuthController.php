<?php

namespace App\Http\Controllers;

use App\Services\TextBeeSmsService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecoveryCodeMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite; // INTEGRATED
use Illuminate\Support\Str; // INTEGRATED
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{   
    // --- START GOOGLE INTEGRATION METHODS ---
    public function redirectToGoogle() {
        // $myRedirect = 'http://127.0.0.1:8000/auth/google/callback';
        // config(['services.google.redirect' => $myRedirect]);
       
        return Socialite::driver('google')->redirect();
        
        //return Socialite::driver('google')->redirect()->getTargetUrl();
    }

    public function handleGoogleCallback() {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('Email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create user if they don't exist
                $user = User::create([
                    'Fullname' => $googleUser->getName(),
                    'Username' => $googleUser->getEmail(),
                    'Email'    => $googleUser->getEmail(),
                    'Password' => Hash::make(Str::random(24)),
                    'Gender'   => 'Other', 
                    'Contact_Number' => 'N/A', 
                    'Profile_Picture' => $googleUser->getAvatar(),
                    'Role_Id' => 2,
                    'Account_Status' => 'Active'
                ]);
            }

            Auth::login($user);
            return redirect()->intended('/member/portal');

        } catch (\Exception $e) {
            Log::error("Google Login Error: " . $e->getMessage());
            return redirect('/login')->withErrors(['error' => 'Google authentication failed.']);
        }
    }
    // --- END GOOGLE INTEGRATION METHODS ---

    public function showLogin() {
        return view('auth.loginpage');
    }

    public function showRegister() {
        return view('auth.signuppage');
    }

    public function register(Request $request){
        $request->validate([
            'username' => 'required|unique:user,Username',
            'email' => 'required|email|unique:user,Email',
            'password' => 'required|min:7',
        ]);

        $profile = "";
        if ($request->gender == 'Male') {
            $profile = 'profile-male.png';
        } elseif ($request->gender == 'Female') {
            $profile = 'profile-female.png';
        } else {
            $profile = 'profile-others.jpg';
        }

        User::create([
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

        return redirect('/login')->with('success', 'Registration successful! Please log in.');
    }

    public function login(Request $request) {
        $credentials = [
            'Email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $user = Auth::user();

            if ($user->Account_Status === 'Inactive') {
                Auth::logout();
                return back()->withErrors(['error' => 'Your account is currently deactivated.']);
            }

            $request->session()->regenerate();

            if (Auth::user()->Role_Id == 1) {
                return redirect()->intended('/dashboard');
            } else if (Auth::user()->Role_Id == 2) {
                return redirect()->intended('/member/portal');
            }
        }

        return back()->withErrors(['error' => 'Incorrect password or username.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect() ->route('landing');
    }

    public function showForgotPassword() {
        return view('auth.forgot-password');
    }

    public function handleRecovery(Request $request){
        $step = $request->step;

        if ($step === 'send_code') {
            $identifier = $request->identifier;
            \Log::info('Recovery attempt for: ' . $identifier);

            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $user = User::where('Email', $identifier)->first();
                if (!$user) return response()->json(['status' => 'error', 'message' => 'Email not found.']);

                $otp = rand(100000, 999999);
                session(['reset_otp' => $otp, 'reset_user_id' => $user->User_id]);

                try {
                    Mail::to($user->Email)->send(new RecoveryCodeMail($otp));
                    return response()->json(['status' => 'success']);
                } catch (\Exception $e) {
                    Log::error("Email OTP failed: " . $e->getMessage());
                    return response()->json(['status' => 'error', 'message' => 'Failed to send email.']);
                }
            }
            
            elseif (preg_match('/^[0-9+]{10,15}$/', $identifier)) {
                $user = User::where('Contact_Number', $identifier)->first();
                if (!$user) return response()->json(['status' => 'error', 'message' => 'Phone number not found.']);

                $cleanNumber = preg_replace('/[^0-9]/', '', $identifier);
                if (str_starts_with($cleanNumber, '0')) {
                    $normalizedNumber = '+63' . substr($cleanNumber, 1);
                } elseif (str_starts_with($cleanNumber, '63')) {
                    $normalizedNumber = '+' . $cleanNumber;
                } else {
                    $normalizedNumber = '+63' . $cleanNumber;
                }

                $otp = rand(100000, 999999);
                session(['reset_otp' => $otp, 'reset_user_id' => $user->User_id]);
                $message = "Your Cornerstone OTP is: $otp";
                
                $apiKey = config('services.textbee.api_key');
                $deviceId = config('services.textbee.device_id');
                
                try {
                    $response = Http::withHeaders([
                        'x-api-key' => $apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->withoutVerifying() 
                    ->post("https://api.textbee.dev/api/v1/gateway/devices/{$deviceId}/send-sms", [
                        'recipients' => [$normalizedNumber],
                        'number'     => $normalizedNumber,
                        'message'    => $message,
                    ]);

                    if ($response->successful()) {
                        return response()->json(['status' => 'success']);
                    } else {
                        return response()->json(['status' => 'error', 'message' => 'OTP failed to Send.']);
                    }
                } catch (\Exception $e) {
                    return response()->json(['status' => 'error', 'message' => 'Could not connect to SMS provider.']);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'Invalid format. Use email or phone.']);
            }
        }

        if ($step === 'verify_code') {
            $submittedCode = $request->full_code;
            $storedCode = session('reset_otp');
            if ($storedCode && $submittedCode == $storedCode) {
                session(['reset_verified' => true]);
                return response()->json(['status' => 'success']);
            }
            return response()->json(['status' => 'error', 'message' => 'Invalid or expired code.']);
        }

        if ($step === 'update_password') {
            $userId = session('reset_user_id');
            if (!session('reset_verified')) return response()->json(['status' => 'error', 'message' => 'Not verified.']);
            
            $user = User::find($userId);
            if (!$user) return response()->json(['status' => 'error', 'message' => 'User not found.']);

            $user->Password = Hash::make($request->password);
            $user->save();
            session()->forget(['reset_otp', 'reset_user_id', 'reset_verified']);
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid request step.']);
    }

    public function googleDemoLogin(Request $request) {
        $user = User::where('Role_Id', 1)->first(); 

        if ($user) {
            Auth::login($user);
            return response()->json(['status' => 'success', 'redirect' => '/dashboard']);
        }

        return response()->json(['status' => 'error', 'message' => 'No demo user found.']);
    }

    public function boot(): void
    {
        // Force all generated links to use HTTPS when on Ngrok
        if (str_contains(config('app.url'), 'ngrok-free.dev')) {
            URL::forceScheme('https');
        }
    }
    
}