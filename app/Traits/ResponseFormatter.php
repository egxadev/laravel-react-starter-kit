<?php

namespace App\Traits;

trait ResponseFormatter
{
    /**
     * Success Response
     *
     * @param mixed $data
     * @param string $message
     * @return array
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
     * @param string $message
     * @param mixed $errors
     * @return array
     */
    protected function errorResponse(string $message = 'Error', $errors = null): array
    {
        return [
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ];
    }

    /**
     * Paginated Response
     *
     * @param mixed $data
     * @param array $meta
     * @param array $filters
     * @param string $message
     * @return array
     */
    protected function paginatedResponse($data, array $meta, array $filters = [], string $message = 'Success'): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'filters' => $filters
        ];
    }
} 