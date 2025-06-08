<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class BaseAPI extends Controller
{
    protected function getService()
    {
        return null;
    }

    function successResponse($data = null, $message = null, $code = 200)
    {
        if ($data == null) {
            $code = 201;
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => null,
            ], $code);
        }
        if (!is_int($code) || $code < 100 || $code >= 600) {
            $code = 200;
        }
        if ($message == null) {
            $message = 'Operation completed successfully.';
        }
        if (is_array($data) && count($data) === 0) {
            $code = 204; // No Content
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => null,
            ], $code);
        }
        if (is_object($data) && empty((array)$data)) {
            $code = 204; // No Content
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => null,
            ], $code);
        }
        if ($data instanceof \Illuminate\Support\Collection && $data->isEmpty()) {
            $code = 204; // No Content
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => null,
            ], $code);
        }
        if ($data instanceof \Illuminate\Database\Eloquent\Model && !$data->exists) {
            $code = 204; // No Content
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => null,
            ], $code);
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    function sendResponse($data = null, $message = null, $code = 200)
    {
        return $this->successResponse($data, $message, $code);
    }

    function errorResponse($message = null, $code = 400)
    {
        if (!is_int($code) || $code < 100 || $code >= 600) {
            $code = 500;
        }
        if ($message == null) {
            $message = 'An error occurred while processing your request.';
        }
        if ($code >= 500) {
            $message = 'Internal Server Error: ' . $message;
        } elseif ($code >= 400) {
            $message = 'Client Error: ' . $message;
        }
        if ($code >= 300 && $code < 400) {
            $message = 'Redirection Error: ' . $message;
        }
        if ($code >= 200 && $code < 300) {
            $message = 'Success: ' . $message;
        }
        if ($code >= 100 && $code < 200) {
            $message = 'Informational Response: ' . $message;
        }
        //unauthorized
        if ($code == 401) {
            $message = 'Unauthorized: ' . $message;
        }
        //forbidden
        if ($code == 403) {
            $message = 'Forbidden: ' . $message;
        }
        //not found
        if ($code == 404) {
            $message = 'Not Found: ' . $message;
        }
        //conflict
        if ($code == 409) {
            $message = 'Conflict: ' . $message;
        }
        //unprocessable entity
        if ($code == 422) {
            $message = 'Unprocessable Entity: ' . $message;
        }
        //internal server error
        if ($code == 500) {
            $message = 'Internal Server Error: ' . $message;
        }
        //service unavailable
        if ($code == 503) {
            $message = 'Service Unavailable: ' . $message;
        }
        //log the error message
        if ($code >= 500) {
            Log::error('API Error: ' . $message);
        } elseif ($code >= 400) {
            Log::warning('API Warning: ' . $message);
        } else {
            Log::info('API Info: ' . $message);
        }
        // Log the error message

        Log::error($message);
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }
}
