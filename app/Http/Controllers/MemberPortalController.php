<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MemberPortalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $eventsJoined = EventRegistration::where('User_id', $user->User_id)->count(); 
        
        // ADDED: Notification Count
        $unreadNotifications = $user->unreadNotifications->count();

        return view('memberportal.portal', compact('user', 'eventsJoined', 'unreadNotifications'));
    }

    public function getEvents()
    {
        try {
            $userId = Auth::id();
            $events = Event::with('category')
                ->withCount('registrations')
                ->orderBy('Event_Date', 'asc')
                ->get()
                ->map(function($event) use ($userId) {
                    $event->is_joined = EventRegistration::where('Event_Id', $event->Event_Id)
                        ->where('User_id', $userId)
                        ->exists();
                    return $event;
                });

            return response()->json($events);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ADDED: Mark Read Method
    public function markNotificationsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'success']);
    }

    public function toggleJoin(Request $request)
    {
        $userId = Auth::id();
        $eventId = $request->Event_Id;

        $registration = EventRegistration::where('Event_Id', $eventId)
            ->where('User_id', $userId)
            ->first();

        if ($registration) {
            $registration->delete();
            return response()->json(['status' => 'left']);
        } else {
            EventRegistration::create([
                'User_id' => $userId,
                'Event_Id' => $eventId
            ]);
            return response()->json(['status' => 'joined']);
        }
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $user = auth()->user();
            $file = $request->file('profile_photo');
            $imageDir = public_path('images');

            // Ensure directory is writable
            if (!File::exists($imageDir)) {
                File::makeDirectory($imageDir, 0755, true);
            }

            $filename = time() . '_' . $user->User_id . '.' . $file->getClientOriginalExtension();

            // FIXED: Using standard move() for best compatibility with Render's permission system
            $file->move($imageDir, $filename);

            // Delete old photo (if not a default asset)
            if ($user->Profile_Picture && File::exists($imageDir . '/' . $user->Profile_Picture)) {
                $defaults = ['profile-male.png', 'profile-female.png', 'profile-others.jpg', 'profile-male.jpg'];
                if (!in_array($user->Profile_Picture, $defaults)) {
                    File::delete($imageDir . '/' . $user->Profile_Picture);
                }
            }

            $user->Profile_Picture = $filename;
            $user->save();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}