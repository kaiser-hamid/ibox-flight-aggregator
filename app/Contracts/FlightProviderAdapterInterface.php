<?php

namespace App\Contracts;

use App\DTOs\FlightDTO;

interface FlightProviderAdapterInterface
{
    /** @return FlightDTO[] */
    public function getFlights(): array;
}
