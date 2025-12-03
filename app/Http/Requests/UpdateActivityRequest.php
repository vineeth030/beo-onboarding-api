<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
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
            'employee_id' => ['sometimes', 'integer', 'exists:employees,id'],
            'performed_by_user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'user_type' => ['sometimes', 'string', 'in:candidate,hr,superadmin'],
            'type' => ['sometimes', 'string', 'max:255'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'payload' => ['nullable', 'array'],
            'ip_address' => ['nullable', 'string', 'max:255'],
            'user_agent' => ['nullable', 'string'],
        ];
    }
}
