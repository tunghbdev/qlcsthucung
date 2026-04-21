<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staffs';
    
    protected $fillable = [
        'user_id',
        'staff_code',
        'position',
        'specialization',
        'hire_date',
        'rating',
        'total_appointments'
    ];

    protected $casts = [
        'hire_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staffServices()
    {
        return $this->hasMany(StaffService::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
