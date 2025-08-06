<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends BaseRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'schedule_id' => 'required|uuid|exists:schedules,id',
            'passengers' => 'required|array',
            'passengers.*.name' => 'required|string|max:255|min:2',
            'passengers.*.id_number' => 'required|string|max:255',
            'passengers.*.status' => 'required|string|in:child,adult',
        ];
    }
}
