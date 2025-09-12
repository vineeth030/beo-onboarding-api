<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'client_id' => ['required'],
            'dob' => ['sometimes', 'date'],
            'gender' => ['sometimes', 'string'],
            'marital_status' => ['sometimes', 'string'],
            'nationality' => ['sometimes', 'string'],
            'place_of_birth' => ['sometimes', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:employees'],
            'mobile' => ['required', 'string', 'max:255', 'unique:employees'],
            'status' => ['sometimes', 'integer', 'in:0,1,2,3,4'],
            'offer_letter_status' => ['sometimes', 'integer', 'in:0,1,2'],
            'division' => ['sometimes', 'integer', 'in:0,1'],
        ];
    }
}
