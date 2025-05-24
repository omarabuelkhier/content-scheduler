<?php

namespace App\Helpers;

class ResponseHelper
{
    /**
     * Generate a standardized JSON success response.
     *
     * @param string $message
     * @param mixed $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function success(string $message, $data = null, int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Generate a standardized JSON error response.
     *
     * @param string $message
     * @param int $status
     * @param mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function error(string $message, int $status = 200, $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }  //
}
