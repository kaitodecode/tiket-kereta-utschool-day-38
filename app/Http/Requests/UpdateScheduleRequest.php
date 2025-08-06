<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'train_id' => ['required', 'uuid', 'exists:trains,id'],
            'route_id' => ['required', 'uuid', 'exists:routes,id'],
            'departure_time' => ['required', 'date'],
            'arrival_time' => ['required', 'date', 'after:departure_time'],
            'price' => ['required', 'numeric', 'min:0']
        ];
    }
}
