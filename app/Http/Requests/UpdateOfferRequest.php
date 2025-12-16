<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferRequest extends FormRequest
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
            'content' => ['sometimes', 'string'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'employee_id' => ['sometimes', 'exists:employees,id'],
            'department_id' => ['sometimes', 'exists:clients,id'],
            'name' => ['sometimes'],
            'comment' => ['sometimes'],
            'sign_file_path' => ['sometimes'],
            'is_accepted' => ['sometimes'],
            'is_declined' => ['sometimes'],
            'decline_reason' => ['sometimes']
        ];
    }
}