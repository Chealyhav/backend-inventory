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

        Log::error($message);
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }
}
