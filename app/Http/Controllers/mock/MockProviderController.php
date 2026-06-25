<?php

namespace App\Http\Controllers\mock;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MockProviderController extends Controller
{
    private const array MOCK_DATA_PATH = [
        'provider_a' => 'mock-data/flight_provider_data_a.json',
        'provider_b' => 'mock-data/flight_provider_data_b.json',
        'provider_c' => 'mock-data/flight_provider_data_c.json',
    ];
    public function ProviderA(): JsonResponse
    {
        $data = json_decode( file_get_contents(base_path(self::MOCK_DATA_PATH['provider_a'])),true );

        return response()->json($data);
    }

    public function ProviderB(): JsonResponse
    {
        $data = json_decode( file_get_contents(base_path(self::MOCK_DATA_PATH['provider_b'])),true );

        return response()->json($data);
    }

    public function ProviderC(): JsonResponse
    {
        $data = json_decode( file_get_contents(base_path(self::MOCK_DATA_PATH['provider_c'])),true );

        return response()->json($data);
    }
}
