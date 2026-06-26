<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('test', function (Request $request) {
    $data = app(\App\Services\FlightAggregatorService::class)->search([]);
    return $data;
});
