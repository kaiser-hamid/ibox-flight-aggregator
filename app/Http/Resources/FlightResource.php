<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class FlightResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'flight_id'      => $this->flightId,
            'flight_number'  => $this->flightNumber,
            'carrier'        => $this->carrier,
            'from'           => $this->from,
            'to'             => $this->to,
            'departure_time' => Carbon::parse($this->departureTime)->toIso8601String(),
            'arrival_time'   => Carbon::parse($this->arrivalTime)->toIso8601String(),
            'duration_mins'  => (int) Carbon::parse($this->departureTime)->diffInMinutes(Carbon::parse($this->arrivalTime)),
            'stops'          => $this->stops,
            'price'          => $this->price,
            'currency'       => $this->currency,
            'source'       => $this->source
        ];
    }
}
