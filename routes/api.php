<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('test', function (Request $request) {
    $data = (new \App\Adapters\FlightProviderCAdapter)->getFlights();
    return $data;
});
