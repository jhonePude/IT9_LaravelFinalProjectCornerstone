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

    // Ensure images directory exists
    $imageDir = public_path('images');
    if (!File::exists($imageDir)) {
        File::makeDirectory($imageDir, 0755, true);
    }

    // Generate a unique filename
    $filename = time() . '_' . $user->User_id . '.' . $file->getClientOriginalExtension();
    $targetPath = $imageDir . '/' . $filename;

    // Try to resize using GD
    $gdEnabled = extension_loaded('gd');
        if ($gdEnabled && function_exists('imagecreatefromjpeg')) {
            try {
                $resized = $this->resizeImage($file);
                if ($resized) {
                    file_put_contents($targetPath, $resized);
                } else {
                    // Fallback to original file
                    $file->move($imageDir, $filename);
                    Log::info('GD resize failed, using original file.');
                }
            } catch (\Exception $e) {
                // If anything fails, just move the original
                $file->move($imageDir, $filename);
                Log::error('GD error: ' . $e->getMessage());
            }
        } else {
            // GD not available – just move
            $file->move($imageDir, $filename);
            Log::info('GD not available, using original file.');
        }

        // Delete old profile picture
        if ($user->Profile_Picture && file_exists($imageDir . '/' . $user->Profile_Picture)) {
            unlink($imageDir . '/' . $user->Profile_Picture);
        }

        $user->Profile_Picture = $filename;
        $user->save();

        return response()->json(['success' => true]);
    }

    /**
     * Resize image to 500x500 square (cropping to center) using GD.
     * Returns the image data as a string, or false on failure.
     */
    private function resizeImage($file)
    {
        $source = null;
        $mime = $file->getMimeType();

        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $source = imagecreatefromjpeg($file->getPathname());
                break;
            case 'image/png':
                $source = imagecreatefrompng($file->getPathname());
                imagealphablending($source, true);
                imagesavealpha($source, true);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($file->getPathname());
                break;
            default:
                return false;
        }

        if (!$source) return false;

        $origWidth = imagesx($source);
        $origHeight = imagesy($source);

        // Determine crop area to make it square (center)
        $cropSize = min($origWidth, $origHeight);
        $cropX = ($origWidth - $cropSize) / 2;
        $cropY = ($origHeight - $cropSize) / 2;

        // Create a new true colour image for the cropped version
        $cropped = imagecreatetruecolor($cropSize, $cropSize);
        if ($mime === 'image/png') {
            imagealphablending($cropped, false);
            imagesavealpha($cropped, true);
        }
        imagecopyresampled($cropped, $source, 0, 0, $cropX, $cropY, $cropSize, $cropSize, $cropSize, $cropSize);

        // Resize to 500x500 (target size)
        $targetSize = 500;
        $resized = imagecreatetruecolor($targetSize, $targetSize);
        if ($mime === 'image/png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }
        imagecopyresampled($resized, $cropped, 0, 0, 0, 0, $targetSize, $targetSize, $cropSize, $cropSize);

        // Save to output buffer
        ob_start();
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                imagejpeg($resized, null, 90);
                break;
            case 'image/png':
                imagepng($resized, null, 9);
                break;
            case 'image/gif':
                imagegif($resized);
                break;
        }
        $imageData = ob_get_clean();

        // Free memory
        imagedestroy($source);
        imagedestroy($cropped);
        imagedestroy($resized);

        return $imageData;
    }
    
}