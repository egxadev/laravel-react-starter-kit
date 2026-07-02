<?php

namespace App\Traits;

trait ResponseFormatter
{
    /**
     * Success Response
     *
     * @param  mixed  $data
     */
    protected function successResponse($data = null, string $message = 'Success'): array
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data) {
            $response['data'] = $data;
        }

        return $response;
    }

    /**
     * Error Response
     *
     * @param  mixed  $errors
     */
    protected function errorResponse(string $message = 'Error', $errors = null): array
    {
        return [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ];
    }

    /**
     * Paginated Response
     *
     * @param  mixed  $data
     */
    protected function paginatedResponse($data, array $meta, array $filters = [], string $message = 'Success'): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'filters' => $filters,
        ];
    }
}
