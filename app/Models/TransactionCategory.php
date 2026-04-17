<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    protected $table = 'transaction_categories';
    protected $primaryKey = 'Category_Id';
    public $timestamps = false;
    protected $fillable = ['Category_Name'];
}