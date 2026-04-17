<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; 
class User extends Authenticatable
{
    use Notifiable, HasFactory;

    // 1. Tell Laravel your table is 'user', not 'users'
    protected $table = 'user';

    // 2. Tell Laravel your Primary Key is 'User_id' (based on your PHP code)
    protected $primaryKey = 'User_id';

    protected $rememberTokenName = 'remember_token';

    // 3. Update fillable to match your church_managment_db columns EXACTLY
    protected $fillable = [
        'Fullname',
        'Username',
        'Email',
        'Password',
        'Gender',
        'Contact_Number',
        'Profile_Picture',
        'Account_Status',
        'Role_Id',
    ];


    // 4. Tell Laravel your password column is 'Password' (Capital P)
    public function getAuthPassword()
    {
        return $this->Password;
    }
    
    // Disable timestamps if your 'user' table doesn't have created_at/updated_at
    public $timestamps = false; 
    

     public function role() {
        return $this->belongsTo(Role::class, 'Role_Id', 'Role_Id');
    }
}


