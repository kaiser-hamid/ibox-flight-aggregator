<?php

namespace App\Services;

use App\Adapters\FlightProviderAAdapter;
use App\Adapters\FlightProviderBAdapter;
use App\Adapters\FlightProviderCAdapter;
use App\DTOs\FlightDTO;
use App\Models\FlightCache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FlightAggregatorService
{
    public function __construct(
        private FlightProviderAAdapter $flightProviderA,
        private FlightProviderBAdapter $flightProviderB,
        private FlightProviderCAdapter $flightProviderC,
    )
    {}


    public function search(array $params = []): array
    {
        $allFlights = $this->fetchFromProviders();

        // parsing the lowest price record from duplicate flights
        $deDuplicatedFlights = $this->deDuplicate($allFlights);

        //filter flight data
        $filteredFlights = $this->filter($deDuplicatedFlights, $params);

        //Sort flight data
        $sortedFlights = $this->sort($filteredFlights, $params);

        $results = $sortedFlights->values()->all();

        // Cache results so booking can look them up without trusting the client
        $this->cacheFlights($results);

        return $results;
    }

    private function fetchFromProviders(): Collection
    {
        return collect([
            ...$this->flightProviderA->getFlights(),
            ...$this->flightProviderB->getFlights(),
            ...$this->flightProviderC->getFlights(),
        ]);
    }

    private function deDuplicate(Collection $flights): Collection
    {
        return $flights
            ->groupBy( fn (FlightDTO $f) => $f->flightId)
            ->map(fn (Collection $group) => $group->sortBy('price')->first())
            ->values();
    }

    private function filter(Collection $flights, array $params): Collection
    {
        return $flights
            ->when(
                isset($params['from']),
                fn ($c) => $c->filter(
                    fn (FlightDTO $f) => $f->from === strtoupper($params['from'])
                )
            )
            ->when(
                isset($params['to']),
                fn ($c) => $c->filter(
                    fn (FlightDTO $f) => $f->to === strtoupper($params['to'])
                )
            )
            ->when(
                isset($params['date']),
                fn ($c) => $c->filter(
                    fn (FlightDTO $f) => Carbon::parse($f->departureTime)->isSameDay(Carbon::parse($params['date']))
                )
            )
            ->when(
                isset($params['max_price']),
                fn ($c) => $c->filter(
                    fn (FlightDTO $f) => $f->price <= (float) $params['max_price']
                )
            )
            ->when(
                isset($params['stops']),
                fn ($c) => $c->filter(
                    fn (FlightDTO $f) => $f->stops === (int) $params['stops']
                )
            );
    }

    private function sort(Collection $flights, array $params): Collection
    {
        return match ($params['sort_by'] ?? 'price') {
            'departure' => $flights->sortBy(fn (FlightDTO $f) => $f->departureTime),
            default => $flights->sortBy(fn (FlightDTO $f) => $f->price)
        };
    }

    public function getProviderCount(): int
    {
        return 3;
    }

    private function cacheFlights(array $flights): void
    {
        $expiresAt = now()->addMinutes(30);

        foreach ($flights as $flight) {
            FlightCache::updateOrCreate(
                ['flight_id' => $flight->flightId],
                [
                    'flight_data' => [
                        'flight_id'      => $flight->flightId,
                        'flight_number'  => $flight->flightNumber,
                        'carrier'        => $flight->carrier,
                        'from'           => $flight->from,
                        'to'             => $flight->to,
                        'departure_time' => $flight->departureTime,
                        'arrival_time'   => $flight->arrivalTime,
                        'stops'          => $flight->stops,
                        'price'          => $flight->price,
                        'currency'       => $flight->currency,
                        'source'         => $flight->source,
                    ],
                    'expires_at' => $expiresAt,
                ]
            );
        }
    }

    public function findFlight(string $flightId): array
    {
        $cached = FlightCache::where('flight_id', $flightId)
            ->where('expires_at', '>', now())
            ->latest()
            ->firstOrFail();

        return $cached->flight_data;
    }

}
