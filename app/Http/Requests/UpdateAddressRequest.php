<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'line1' => ['sometimes', 'required', 'string', 'max:255'],
            'country' => ['sometimes', 'required', 'string', 'max:255'],
            'state' => ['sometimes', 'required', 'string', 'max:255'],
            'pin' => ['sometimes', 'required', 'string', 'max:255'],
            'duration_of_stay' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}
