<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PunchLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'punch_date_time',
        'user_code',
        'punch_status',
        'send_message',
        'is_process',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'punch_date_time' => 'datetime',
        'punch_status' => 'boolean',
        'send_message' => 'boolean',
        'is_process' => 'boolean',
    ];

    /**
     * Get the user that owns the punch log.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_code', 'user_code');
    }
}
