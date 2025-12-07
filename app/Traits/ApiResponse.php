<?php

namespace App\Traits;

Trait ApiResponse {

    function success($data = null, string $message = "", string $status = "success", int $responseStatus = 200)
    {
        return response()->json([
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ], $responseStatus);
    }

    function fail(string $error = "" , string $status = "fail" , int $responseStatus = 400)
    {
        return response()->json([
            'status'  => $status,
            'error'   => $error
        ], $responseStatus);
    }
}
