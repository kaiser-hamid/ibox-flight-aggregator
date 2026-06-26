<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'reference'   => $this->reference,
            'flight_id'   => $this->flight_id,
            'flight_data' => $this->flight_data,
            'passengers'  => $this->passengers,
            'total_price' => $this->total_price,
            'currency'    => $this->currency,
            'created_at'  => Carbon::parse($this->created_at)->toIso8601String(),
        ];

    }
}
