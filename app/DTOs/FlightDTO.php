<?php

namespace App\DTOs;

class FlightDTO
{
    public function __construct(
        public readonly string $flightId,
        public readonly string $flightNumber,
        public readonly string $carrier,
        public readonly string $from,
        public readonly string $to,
        public readonly string $departureTime,
        public readonly string $arrivalTime,
        public readonly int    $stops,
        public readonly float  $price,
        public readonly string $currency,
        public readonly string $source,
    ) {}
}
