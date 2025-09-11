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
            'dob' => ['required', 'date'],
            'gender' => ['required', 'string'],
            'marital_status' => ['required', 'string'],
            'nationality' => ['required', 'string'],
            'place_of_birth' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:employees'],
            'mobile' => ['required', 'string', 'max:255', 'unique:employees'],
        ];
    }
}
