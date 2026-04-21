<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = [
        'customer_id',
        'name',
        'type',
        'breed',
        'age',
        'weight',
        'color',
        'description',
        'health_notes',
        'last_checkup',
        'image_path',
        'is_active'
    ];

    protected $casts = [
        'last_checkup' => 'date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
