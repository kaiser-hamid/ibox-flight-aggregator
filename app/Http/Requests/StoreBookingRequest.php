<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'flight_id'              => 'required|string',

            'flight_data'            => 'required|array',
            'flight_data.flight_id'  => 'required|string',
            'flight_data.carrier'    => 'required|string',
            'flight_data.from'       => 'required|string|size:3',
            'flight_data.to'         => 'required|string|size:3',
            'flight_data.price'      => 'required|numeric|min:0',
            'flight_data.currency'   => 'required|string|size:3',
            'flight_data.source'     => 'required|string',

            'passengers'             => 'required|array|min:1',
            'passengers.*.name'      => 'required|string',
            'passengers.*.email'     => 'required|string',
            'passengers.*.passport'  => 'required|string',
        ];
    }
}
