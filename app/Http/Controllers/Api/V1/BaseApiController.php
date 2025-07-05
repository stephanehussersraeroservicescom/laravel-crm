<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BaseApiController extends Controller
{
    /**
     * Success response method.
     */
    protected function sendResponse($result, $message = null, $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $result,
        ];

        if (!is_null($message)) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }

    /**
     * Error response method.
     */
    protected function sendError($error, $errorMessages = [], $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * Validation error response method.
     */
    protected function sendValidationError(ValidationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $exception->errors(),
        ], 422);
    }

    /**
     * Resource not found response.
     */
    protected function sendNotFound($message = 'Resource not found'): JsonResponse
    {
        return $this->sendError($message, [], 404);
    }

    /**
     * Unauthorized response.
     */
    protected function sendUnauthorized($message = 'Unauthorized'): JsonResponse
    {
        return $this->sendError($message, [], 401);
    }

    /**
     * Forbidden response.
     */
    protected function sendForbidden($message = 'Forbidden'): JsonResponse
    {
        return $this->sendError($message, [], 403);
    }

    /**
     * Server error response.
     */
    protected function sendServerError($message = 'Internal server error'): JsonResponse
    {
        return $this->sendError($message, [], 500);
    }

    /**
     * Get pagination info from request.
     */
    protected function getPaginationParams(Request $request): array
    {
        return [
            'per_page' => min($request->get('per_page', 15), 100),
            'page' => $request->get('page', 1),
        ];
    }

    /**
     * Get search params from request.
     */
    protected function getSearchParams(Request $request): array
    {
        return [
            'search' => $request->get('search'),
            'sort_by' => $request->get('sort_by', 'id'),
            'sort_direction' => $request->get('sort_direction', 'desc'),
        ];
    }
}