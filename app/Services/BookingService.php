<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class BookingService
{
    public function __construct(
        private FlightAggregatorService $flightAggregatorService
    ) {}

    public function create(array $data): Booking
    {
        // Resolve flight data from server-side cache
        try {
            $flightData = $this->flightAggregatorService->findFlight($data['flight_id']);
        } catch (ModelNotFoundException) {
            throw new UnprocessableEntityHttpException(
                'Flight not found or search session expired. Please search again.'
            );
        }

        return Booking::create([
            'reference'   => $this->generateReference(),
            'flight_id'   => $data['flight_id'],
            'flight_data' => $flightData,
            'passengers'  => $data['passengers'],
            'total_price' => $flightData['price'] * count($data['passengers']),
            'currency'    => $flightData['currency'],
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
