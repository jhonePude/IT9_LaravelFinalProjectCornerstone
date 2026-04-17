<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index() {
        return view('users.index');
    }





    // 1. FETCH ALL USERS (Replaces users_select.php)
    public function getUsers() {
        $users = User::select('user.*', 'roles.Role_Name')
            ->join('roles', 'user.Role_Id', '=', 'roles.Role_Id')
            ->get();
            
        return response()->json($users);
    }



    // 2. ADD NEW MEMBER (Replaces users_insert.php)
    public function store(Request $request) {
        try {
            // Profile Picture Logic
            $profile = "profile-others.jpg";
            if ($request->gender == 'Male') {
                $profile = 'profile-male.png';
            } elseif ($request->gender == 'Female') {
                $profile = 'profile-female.png';
            }

            // Map Lowercase Request to PascalCase Database
            User::create([
                'Fullname'        => $request->fullname,
                'Username'        => $request->username,
                'Email'           => $request->email,
                'Contact_Number'  => $request->contact,
                'Gender'          => $request->gender,
                'Password'        => Hash::make('CRNS1234567'), // Default for Add
                'Account_Status'  => 'Active',                 // Default Status
                'Profile_Picture' => $profile,
                'Role_Id'         => DB::table('roles')->where('Role_Name', $request->role)->value('Role_Id') ?? 2
            ]);

            return response("success");
        } catch (\Exception $e) {
            return response("Database Error: " . $e->getMessage(), 500);
        }
    }



    
    // 3. UPDATE MEMBER (Replaces users_update.php)
    public function update(Request $request) {
        try {
            $user = User::where('User_id', $request->User_id)->first();
            if (!$user) return response("User not found", 404);

            $roleId = DB::table('roles')->where('Role_Name', $request->role)->value('Role_Id');

            $user->update([
                'Fullname'       => $request->fullname,
                'Username'       => $request->username,
                'Email'          => $request->email,
                'Contact_Number' => $request->contact,
                'Gender'         => $request->gender,
                'Role_Id'        => $roleId
            ]);

            return response("success");
        } catch (\Exception $e) {
            return response("Update Error: " . $e->getMessage(), 500);
        }
    }




    // 4. RESET PASSWORD (Replaces users_update_pass.php)
    public function resetPassword(Request $request) {
        $user = User::where('Email', $request->Email)->first();
        if ($user) {
            // Per your request: CRNS12345657
            $user->Password = Hash::make("CRNS1234567"); 
            $user->save();              
            return response()->json(["status" => "success"]);
        }
        return response()->json(["status" => "error"], 400);
    }

    // 5. TOGGLE STATUS (Replaces users_deactivate_account.php)
    public function toggleStatus(Request $request) {
        // Find user by User_id and update status to Active or Inactive
        $user = User::where('User_id', $request->User_id)->first();
        if ($user) {
            $user->Account_Status = $request->Account_Status;
            $user->save();
            return response("success");
        }
        return response("error");
    }
}