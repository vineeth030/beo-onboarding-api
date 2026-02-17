<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
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
            'id' => ['required', 'integer', 'unique:departments'],
            'name' => ['required', 'string', 'max:255'],
            'notice_period' => ['sometimes', 'integer', 'min:0'],
            'is_family_insurance_paid_by_client' => ['sometimes', 'boolean'],
            'sessionToken' => ['required', 'string'],
            'userIdCode' => ['required', 'integer'],
            'emails' => ['sometimes', 'array'],
            'emails.*' => ['email', 'max:255'],
        ];
    }
}
