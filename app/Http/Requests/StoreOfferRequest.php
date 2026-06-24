<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfferRequest extends FormRequest
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
            'email_attachment_content_for_client' => ['required', 'string'],
            'email_content_for_employee' => ['required', 'string'],
            'user_id' => ['required', 'exists:users,id'],
            'employee_id' => ['required', 'exists:employees,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'designation_id' => ['required', 'exists:designations,id'],
            'client_emails' => ['required', 'array'],
            // 'client_emails.*' => ['required', 'array:email,name'],
            // 'client_emails.*.email' => ['required', 'email'],
            // 'client_emails.*.name' => ['required', 'string'],
            'beo_emails' => ['required', 'array'],
            // 'beo_emails.*' => ['required', 'array:email,name'],
            // 'beo_emails.*.email' => ['required', 'email'],
            // 'beo_emails.*.name' => ['required', 'string'],
            'is_revised' => ['sometimes'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_emails.*.name.required' => 'Each email must have a name.',
            'beo_emails.*.name.required' => 'Each email must have a name.',
        ];
    }
}
