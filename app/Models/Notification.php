<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'read_at',
    ];

    // Optional: define relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Optional: check if notification is read
    public function isRead()
    {
        return !is_null($this->read_at);
    }
    
}
