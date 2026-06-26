<?php

namespace App\Traits;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    protected function success(
        mixed $data,
        string $message = 'Success',
        int $status = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    protected function error(
        string $message,
        int $status = Response::HTTP_BAD_REQUEST,
        mixed $errors = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }
}
