<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchFlightRequest;
use App\Http\Resources\FlightCollection;
use App\Services\FlightAggregatorService;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function __construct(private FlightAggregatorService $flightAggregatorService)
    {}

    public function search(SearchFlightRequest $request): FlightCollection
    {
        $params = $request->validated();

        $flights = $this->flightAggregatorService->search($params);

        return new FlightCollection(
            resource: $flights,
            providersQueried: $this->flightAggregatorService->getProviderCount(),
            providersSucceeded: $this->flightAggregatorService->getProviderCount(),
            filtersApplied: $params,
        );

    }
}
