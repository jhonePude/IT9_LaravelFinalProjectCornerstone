<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $table = 'portal_event_registrations';
    protected $primaryKey = 'Registration_Id';
    public $timestamps = false;

    protected $fillable = ['User_id', 'Event_Id', 'Registration_Date'];
}