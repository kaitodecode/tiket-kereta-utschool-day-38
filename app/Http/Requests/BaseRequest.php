<?php

namespace App\Http\Requests;

use App\Traits\HasResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        Log::error('Validation failed:', $validator->errors()->toArray());

        throw new \Illuminate\Validation\ValidationException(
            $validator,
            response()->json([
                'success' => false,
                'message' => 'Bad Request',
                'data' => $validator->errors()
            ], 422)
        );
    }
}
