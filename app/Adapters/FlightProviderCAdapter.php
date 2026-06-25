<?php

namespace App\Adapters;

use App\Contracts\FlightProviderAdapterInterface;
use App\DTOs\FlightDTO;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class FlightProviderCAdapter implements FlightProviderAdapterInterface
{
    /** @return FlightDTO[] */
    public function getFlights(): array
    {
        $result = Http::get(config('flightprovider.provider_c_url'));

        return array_map(
          fn ($flight) => $this->normalize($flight),
            $result->json('results') ?? []
        );
    }

    private function normalize(array $flightData): FlightDTO
    {
        $arrival = Carbon::parse($flightData['times']['arr']);
        $departure = Carbon::parse($flightData['times']['dep']);
        $date = $departure->toDateString();

        return  new FlightDTO(
            flightId:      "{$flightData['code']}_{$flightData['route']['src']}_{$flightData['route']['dst']}_{$date}",
            flightNumber:  $flightData['code'],
            carrier:       $flightData['iata'],
            from:          $flightData['route']['src'],
            to:            $flightData['route']['dst'],
            departureTime: $departure->toIso8601String(),
            arrivalTime:   $arrival->toIso8601String(),
            stops:         (int) $flightData['layovers'],
            price:         (float) $flightData['total_price'],
            currency:      $flightData['currency'],
            source:        'provider_c',
        );
    }

}
