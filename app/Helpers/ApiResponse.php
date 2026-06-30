<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApiResponse
{
    public static function success(
        $data = null,
        string $message = 'Success',
        int $code = 200,
        $meta = null
    ): JsonResponse {
        $response = [
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];

        if (is_null($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    public static function paginated(
        LengthAwarePaginator $paginator,
        mixed $resource,
        string $message = 'Data Retrieved Successfully'
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resource,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more_page' => $paginator->hasMorePages()
            ],
        ], 200);
    }

    public static function error(
        string $message = 'Error',
        $errors = null,
        int $code = 400
    ): JsonResponse {

        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
