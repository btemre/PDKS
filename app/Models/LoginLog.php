<?php
// app/Models/LoginLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    public $timestamps = false; // logged_in_at var zaten
    protected $fillable = ['user_id', 'session_id', 'status', 'ip_address', 'user_agent', 'logged_in_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

