<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'Event_Id';
    public $timestamps = false;

    protected $casts = [
        'Event_Date' => 'date', // Added formatting directly to the cast
    ];

    // This ensures that when Laravel sends data to the Dashboard, it uses the correct format
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }

    protected $fillable = [
        'Title', 'Description', 'Event_Date', 'Event_Time', 'Location', 'Image_Banner', 'Event_Category_Id'
    ];

    public function category() {
        return $this->belongsTo(EventCategory::class, 'Event_Category_Id', 'Event_Category_Id');
    }

    public function registrations() {
        return $this->hasMany(EventRegistration::class, 'Event_Id', 'Event_Id');
    }
}