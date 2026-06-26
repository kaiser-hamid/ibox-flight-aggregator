<?php

namespace App\Services;

use App\Adapters\FlightProviderAAdapter;
use App\Adapters\FlightProviderBAdapter;
use App\Adapters\FlightProviderCAdapter;
use Symfony\Component\Translation\Provider\ProviderInterface;

class FlightAggregatorService
{
    public function __construct(
        private FlightProviderAAdapter $flightProviderA,
        private FlightProviderBAdapter $flightProviderB,
        private FlightProviderCAdapter $flightProviderC,
    )
    {}


    public function search(): array
    {
        $allFlights = collect([
            ...$this->flightProviderA->getFlights(),
            ...$this->flightProviderB->getFlights(),
            ...$this->flightProviderC->getFlights(),
        ]);


        return ['data' => $allFlights];
    }

}
