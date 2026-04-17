<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    protected $table = 'event_categories';
    protected $primaryKey = 'Event_Category_Id';
    public $timestamps = false;
    protected $fillable = ['Event_Category_Name'];

    public function events() {
        return $this->hasMany(Event::class, 'Event_Category_Id', 'Event_Category_Id');
    }
}