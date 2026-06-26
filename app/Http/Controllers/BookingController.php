<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request, BookingService $bookingService): JsonResponse
    {
        $booking = $bookingService->create($request->validated());

        return (new BookingResource($booking))
            ->additional([
            'status' => true,
            'message' => 'Booking created successfully.'
        ])
        ->response()
        ->setStatusCode(Response::HTTP_CREATED);
    }
}
