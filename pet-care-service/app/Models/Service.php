<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
        'icon'
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function staffServices()
    {
        return $this->hasMany(StaffService::class);
    }
}
