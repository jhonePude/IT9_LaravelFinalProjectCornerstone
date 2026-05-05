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

        $user = auth()->user();
        $file = $request->file('profile_photo');

        // FIX: Ensure public/images exists and is writable on Render
        $imageDir = public_path('images');
        if (!File::exists($imageDir)) {
            File::makeDirectory($imageDir, 0777, true, true);
        }

        $filename = time() . '_' . $user->User_id . '.' . $file->getClientOriginalExtension();
        $targetPath = $imageDir . '/' . $filename;

        try {
            $gdEnabled = extension_loaded('gd');
            if ($gdEnabled) {
                $resizedData = $this->resizeImage($file);
                if ($resizedData) {
                    File::put($targetPath, $resizedData);
                } else {
                    $file->move($imageDir, $filename);
                }
            } else {
                $file->move($imageDir, $filename);
            }

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
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function resizeImage($file)
    {
        $mime = $file->getMimeType();
        if ($mime == 'image/jpeg' || $mime == 'image/jpg') {
            $source = imagecreatefromjpeg($file->getPathname());
        } elseif ($mime == 'image/png') {
            $source = imagecreatefrompng($file->getPathname());
        } elseif ($mime == 'image/gif') {
            $source = imagecreatefromgif($file->getPathname());
        } else {
            return false;
        }

        if (!$source) return false;

        $width = imagesx($source);
        $height = imagesy($source);
        $size = min($width, $height);
        $x = ($width - $size) / 2;
        $y = ($height - $size) / 2;

        $target = imagecreatetruecolor(500, 500);
        
        if ($mime == 'image/png') {
            imagealphablending($target, false);
            imagesavealpha($target, true);
        }

        imagecopyresampled($target, $source, 0, 0, $x, $y, 500, 500, $size, $size);

        ob_start();
        if ($mime == 'image/png') imagepng($target);
        else imagejpeg($target, null, 90);
        $data = ob_get_clean();

        imagedestroy($source);
        imagedestroy($target);

        return $data;
    }
}