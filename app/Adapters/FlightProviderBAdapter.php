<?php

namespace App\Adapters;

use App\Contracts\FlightProviderAdapterInterface;
use App\DTOs\FlightDTO;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class FlightProviderBAdapter implements FlightProviderAdapterInterface
{
    /** @return FlightDTO[] */
    public function getFlights(): array
    {
        $result = Http::get(config('services.flight_providers.provider_b_url'));

        return array_map(
          fn ($flight) => $this->normalize($flight),
            $result->json('data') ?? []
        );
    }

    private function normalize(array $flightData): FlightDTO
    {
        $arrival = Carbon::parse($flightData['arrival_time']);
        $departure = Carbon::parse($flightData['departure_time']);
        $date = $departure->toDateString();

        return  new FlightDTO(
            flightId: "{$flightData['number']}_{$flightData['origin']}_{$flightData['destination']}_{$date}",
            flightNumber: $flightData['number'],
            carrier: $flightData['airline_code'],
            from: $flightData['origin'],
            to: $flightData['destination'],
            departureTime: $departure->toIso8601String(),
            arrivalTime: $arrival->toIso8601String(),
            stops: (int) $flightData['segments'],
            price: (float) $flightData['price']['amount'],
            currency: $flightData['price']['currency'],
            source: 'provider_b',
        );
    }

}
