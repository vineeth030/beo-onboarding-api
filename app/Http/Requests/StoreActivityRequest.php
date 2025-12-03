<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
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
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'performed_by_user_id' => ['required', 'integer', 'exists:users,id'],
            'user_type' => ['required', 'string', 'in:candidate,hr,superadmin'],
            'type' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'payload' => ['nullable', 'array'],
            'ip_address' => ['nullable', 'string', 'max:255'],
            'user_agent' => ['nullable', 'string'],
        ];
    }
}
