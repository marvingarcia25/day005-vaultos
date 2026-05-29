<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'unit_id',
        'lease_start',
        'lease_end',
        'status',
        'emergency_contact',
        'notes',
    ];

    protected $casts = [
        'lease_start' => 'date',
        'lease_end'   => 'date',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->orderByDesc('payment_date');
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
