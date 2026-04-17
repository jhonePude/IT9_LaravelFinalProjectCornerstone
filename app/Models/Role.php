<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'Role_Id';
    protected $fillable = ['Role_Name'];

    public function users() {
        return $this->hasMany(User::class, 'Role_Id', 'Role_Id');
    }
}
