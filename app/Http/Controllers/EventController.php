<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Notifications\NewEventPublished;
use Illuminate\Support\Facades\Notification;

class EventController extends Controller
{
    public function index() {
        $categories = EventCategory::all();
        return view('events.index', compact('categories'));
    }

    public function getData() {
        $events = Event::with('category')
            ->withCount('registrations')
            ->orderBy('Event_Date', 'asc')
            ->get()
            ->map(function($event) {
                // Add a new formatted_date field
                $event->formatted_date = \Carbon\Carbon::parse($event->Event_Date)->format('M d, Y');
                return $event;
            });

        return response()->json($events);
    }

        public function store(Request $request) {
        try {
            $id = $request->Event_Id ?: null;
            $isNew = $id === null;
            
            // 1. Find the Category Name based on the ID sent from the form
            $category = EventCategory::find($request->category_id);
            $categoryName = $category ? $category->Event_Category_Name : 'General';

            $banner = "church1.jpg"; 

            switch($categoryName) {
                case "Worship": 
                    $banner = "church1.jpg"; 
                    break;
                case "Sacrament": 
                    $banner = "sacrament.jpg"; 
                    break;
                case "Youth": 
                    $banner = "youth.jpg"; 
                    break;
                case "Community": 
                    $banner = "community.png"; 
                    break;
                case "Practice": 
                    $banner = "practice.jpg"; 
                    break;
            }

            // 3. Prepare Data
            $data = [
                'Title'             => $request->title,
                'Event_Date'        => $request->date,
                'Event_Time'        => $request->time,
                'Location'          => $request->location,
                'Event_Category_Id' => $request->category_id,
                'Description'       => $request->description ?? 'Join us for this special event at Cornerstone.',
                'Image_Banner'      => $banner // SAVES THE AUTO-PICKED IMAGE TO DATABASE
            ];

            // 4. Save to Database
            $event = Event::updateOrCreate(['Event_Id' => $id], $data);

            if ($isNew) {
                $users = User::all();
                Notification::send($users, new NewEventPublished($event));
            }
        
            return response("success");

        } catch (\Exception $e) {
            return response("Error: " . $e->getMessage(), 500);
        }
    }

    public function destroy($id) {
        $event = Event::find($id);
        if ($event) {
            $event->delete();
            return response("success");
        }
        return response("error", 404);
    }
}