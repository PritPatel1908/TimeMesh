<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_code',
        'first_name',
        'father_name',
        'last_name',
        'mother_name',
        'address',
        'contact_no',
        'guardian_name',
        'guardian_relation',
        'guardian_contact_no',
        'emergency_contact',
        'email',
        'room_number',
        'vehicle_detail',
        'occupation',
        'occupation_address',
        'medical_detail',
        'other_details',
        'left_date',
        'left_remark',
        'joining_date',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'joining_date' => 'datetime',
            'left_date' => 'datetime',
        ];
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the punch logs for the user.
     */
    public function punchLogs()
    {
        return $this->hasMany(PunchLog::class, 'user_code', 'user_code');
    }
}
