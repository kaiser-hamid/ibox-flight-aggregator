<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightCache extends Model
{
    protected $table = 'flight_cache';

    protected $fillable = ['flight_id', 'flight_data', 'expires_at'];

    protected $casts = [
        'flight_data' => 'array',
        'expires_at'  => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
