<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Str;

class BookingService
{
    public function create(array $data): Booking
    {
        return Booking::create([
            'reference'   => $this->generateReference(),
            'flight_id'   => $data['flight_id'],
            'flight_data' => $data['flight_data'],
            'passengers'  => $data['passengers'],
            'total_price' => $data['flight_data']['price'] * count($data['passengers']),
            'currency'    => $data['flight_data']['currency'],
        ]);
    }

    private function generateReference(): string
    {
        do {
            $reference = 'BK-' . strtoupper(Str::random(6));
        } while (Booking::where('reference', $reference)->exists());

        return $reference;
    }

    public function findByReference(string $reference): Booking
    {
        return Booking::where('reference', $reference)->firstOrFail();
    }
}
