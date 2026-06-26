<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'reference',
        'flight_id',
        'flight_data',
        'passengers',
        'total_price',
        'currency',
    ];

    protected $casts = [
        'flight_data' => 'array',
        'passengers'  => 'array',
    ];

}
