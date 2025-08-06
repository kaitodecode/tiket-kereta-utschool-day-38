<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'code' => 'required|string|',
            'latitude' => 'required|string|',
            'longitude' => 'required|string|',
            'city' => 'required|string|',
        ];
    }
}
