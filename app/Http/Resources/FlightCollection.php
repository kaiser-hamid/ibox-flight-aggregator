<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FlightCollection extends ResourceCollection
{
    public $collects = FlightResource::class;

    private int $providersQueried;
    private int $providersSucceeded;
    private array $filtersApplied;

    public function __construct(
        $resource,
        int $providersQueried,
        int $providersSucceeded,
        array $filtersApplied,
    )
    {
        parent::__construct($resource);
        $this->providersQueried = $providersQueried;
        $this->providersSucceeded = $providersSucceeded;
        $this->filtersApplied = $filtersApplied;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Flight list',
            'data'    => $this->collection,
            'meta'    => [
                'total'               => $this->collection->count(),
                'providers_queried'   => $this->providersQueried,
                'providers_succeeded' => $this->providersSucceeded,
                'providers_failed'    => $this->providersQueried - $this->providersSucceeded,
                'filters_applied'     => $this->filtersApplied,
            ],
        ];
    }
}
