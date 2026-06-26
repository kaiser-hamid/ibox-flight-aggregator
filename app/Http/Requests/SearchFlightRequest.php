<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchFlightRequest extends FormRequest
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
            'from'       => 'required|string|size:3|alpha',
            'to'         => 'required|string|size:3|alpha',
            'date'       => 'required|date_format:Y-m-d|after_or_equal:today',
            'passengers' => 'required|integer|min:1|max:9',
            'sort_by'    => 'sometimes|in:price,departure',
            'stops'      => 'sometimes|integer|min:0',
            'max_price'  => 'sometimes|numeric|min:0',
        ];
    }
}
