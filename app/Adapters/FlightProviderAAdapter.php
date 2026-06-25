<?php

namespace App\Adapters;

use App\Contracts\FlightProviderAdapterInterface;
use App\DTOs\FlightDTO;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class FlightProviderAAdapter implements FlightProviderAdapterInterface
{
    /** @return FlightDTO[] */
    public function getFlights(): array
    {
        $result = Http::get(config('services.flight_providers.provider_a_url'));

        return array_map(
          fn ($flight) => $this->normalize($flight),
            $result->json('flights') ?? []
        );
    }

    private function normalize(array $flightData): FlightDTO
    {
        $arrival = Carbon::parse($flightData['arrive']);
        $departure = Carbon::parse($flightData['depart']);
        $date = $departure->toDateString();

        return  new FlightDTO(
            flightId: "{$flightData['flight_no']}_{$flightData['from']}_{$flightData['to']}_{$date}",
            flightNumber: $flightData['flight_no'],
            carrier: $flightData['carrier'],
            from: $flightData['from'],
            to: $flightData['to'],
            departureTime: $departure->toIso8601String(),
            arrivalTime: $arrival->toIso8601String(),
            stops: (int) $flightData['stops'],
            price: (float) $flightData['fare_usd'],
            currency: 'USD',
            source: 'provider_a',
        );
    }

}
