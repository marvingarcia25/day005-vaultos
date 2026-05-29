<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'unit_number',
        'row_label',
        'size_label',
        'size_sqft',
        'monthly_rate',
        'status',
        'floor',
        'notes',
    ];

    protected $casts = [
        'monthly_rate' => 'decimal:2',
        'size_sqft'    => 'integer',
        'floor'        => 'integer',
    ];

    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }
}
