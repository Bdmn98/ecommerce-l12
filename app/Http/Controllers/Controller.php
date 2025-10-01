<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Safe execution time calculator for both web & test (CLI) runs.
     */
    protected function execTimeMs(): string
    {
        $start = \defined('LARAVEL_START')
            ? LARAVEL_START
            : ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true));

        $ms = round((microtime(true) - $start) * 1000, 2);

        return $ms . ' ms';
    }

    /**
     * Standard success JSON response.
     */
    protected function successfulResponse($data = null, string $message = 'OK', int $status = Response::HTTP_OK, array $meta = [])
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta ?: null,
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'execution_time' => $this->execTimeMs(),
        ], $status);
    }

    /**
     * Standard error JSON response.
     */
    protected function errorResponse(
        string $message,
        mixed  $errors = null,
        int    $status = Response::HTTP_BAD_REQUEST
    ): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'execution_time' => $this->execTimeMs(),
        ], $status);
    }

    /**
     * Pagination wrapper response.
     * Accepts a paginator or array-like data + total count.
     */
    protected function jsonResponseWithPagination(
        mixed $data,
        int   $total
    ): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'total' => $total,
            'timestamp' => now()->format('Y-m-d, H:i:s'),
            'execution_time' => $this->execTimeMs(),
        ]);
    }
}
