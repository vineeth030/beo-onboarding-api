<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeBankDetailRequest extends FormRequest
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
            'bank_name' => ['sometimes', 'required', 'string', 'max:255'],
            'account_holder_name' => ['sometimes', 'required', 'string', 'max:255'],
            'account_number' => ['sometimes', 'required', 'string', 'max:255'],
            'branch_name' => ['sometimes', 'required', 'string', 'max:255'],
            'ifsc_code' => ['sometimes', 'required', 'string', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'proof_document' => ['sometimes', 'required', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
            'is_verified' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get the custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ifsc_code.regex' => 'The IFSC code format is invalid.',
        ];
    }
}
