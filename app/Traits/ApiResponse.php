<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponse
{
    /**
     * Return a success response
     */
    protected function successResponse($data = null, string $message = '', int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Alias for successResponse
     */
    protected function success($data = null, string $message = '', int $status = 200): JsonResponse
    {
        return $this->successResponse($data, $message, $status);
    }

    /**
     * Alias for errorResponse
     */
    protected function fail(string $message, int $status = 400, $errors = null): JsonResponse
    {
        return $this->errorResponse($message, $status, $errors);
    }

    /**
     * Return an error response
     */
    protected function errorResponse(string $message, int $status = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Return a paginated response
     */
    protected function paginatedResponse($data, string $message = ''): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data instanceof ResourceCollection) {
            $response['data'] = $data->response()->getData(true)['data'];
            $response['meta'] = [
                'current_page' => $data->response()->getData(true)['meta']['current_page'] ?? null,
                'last_page' => $data->response()->getData(true)['meta']['last_page'] ?? null,
                'per_page' => $data->response()->getData(true)['meta']['per_page'] ?? null,
                'total' => $data->response()->getData(true)['meta']['total'] ?? null,
            ];
        } else {
            $response['data'] = $data->items();
            $response['meta'] = [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ];
        }

        return response()->json($response);
    }

    /**
     * Return a not found response
     */
    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }
}
