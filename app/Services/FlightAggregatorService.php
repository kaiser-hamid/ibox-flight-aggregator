<?php

namespace App\Services;

use App\Adapters\FlightProviderAAdapter;
use App\Adapters\FlightProviderBAdapter;
use App\Adapters\FlightProviderCAdapter;
use App\DTOs\FlightDTO;
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
        //merging flight data into a single collection
        $allFlights = collect([
            ...$this->flightProviderA->getFlights(),
            ...$this->flightProviderB->getFlights(),
            ...$this->flightProviderC->getFlights(),
        ]);

        // parsing the lowest price record from duplicate flights
        $deDuplicatedFlights = $allFlights
            ->groupBy( fn (FlightDTO $f) => $f->flightId)
            ->map(fn (Collection $group) => $group->sortBy('price')->first())
            ->values();

        //filter flight data
        $filteredFlights = $deDuplicatedFlights
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

        //Sort flight data
        $sortedFlights = match ($params['sort_by'] ?? 'price') {
            'departure' => $filteredFlights->sortBy(fn (FlightDTO $f) => $f->departureTime),
            default => $filteredFlights->sortBy(fn (FlightDTO $f) => $f->price)
        };


        return $sortedFlights->values()->all();
    }

    public function getProviderCount(): int
    {
        return 3;
    }

}
