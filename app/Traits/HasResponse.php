<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait HasResponse
{
    public function json($data, $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
