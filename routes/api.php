<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\FlightController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('test', function (Request $request) {
    $cls = app(\App\Services\FlightAggregatorService::class);
    return ['count' => $cls->getProviderCount(), 'data' => $cls->search()];
});

Route::get('flights/search', [FlightController::class, 'search']);

Route::post('bookings', [BookingController::class, 'store']);
Route::get('bookings/{reference}', [BookingController::class, 'show']);
