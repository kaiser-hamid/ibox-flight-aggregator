<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    use ApiResponse;
    public function store(StoreBookingRequest $request, BookingService $bookingService): JsonResponse
    {
        $booking = $bookingService->create($request->validated());

        return $this->success(
            data: new BookingResource($booking),
            message: 'Booking created successfully.',
            status: Response::HTTP_CREATED
        );
    }

    public function show(BookingService $bookingService, string $reference): JsonResponse
    {
        $booking = $bookingService->findByReference($reference);

        return $this->success(
            data: new BookingResource($booking),
            message: 'Booking retrieved successfully.'
        );
    }
}
