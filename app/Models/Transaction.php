<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
    protected $table = 'transactions';
    protected $primaryKey = 'Transaction_Id';
    public $timestamps = false; 

    // This stops the "Field Amount doesn't have a default value" error
    // By allowing all fields to be saved without filtering
    protected $guarded = []; 

    protected $casts = [
        // This fixes the 2026-04-09T00:00:00.000000Z issue
        'Transaction_Date' => 'date:Y-m-d',
    ];
}