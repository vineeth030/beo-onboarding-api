<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmploymentRequest extends FormRequest
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
            'company_name' => ['sometimes', 'required', 'string', 'max:255'],
            'employee_id_at_company' => ['sometimes', 'nullable', 'string', 'max:255'],
            'designation' => ['sometimes', 'required', 'string', 'max:255'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
            'mode_of_employment' => ['sometimes', 'required', 'string', 'max:255'],
            'start_date' => ['sometimes', 'required', 'date'],
            'last_working_date' => ['sometimes', 'nullable', 'date', 'after:start_date'],
            'resignation_acceptance_letter_file' => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'experience_letter_file' => ['sometimes', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'is_current_org' => ['sometimes', 'boolean'],
            'salary_slips' => ['sometimes', 'nullable', 'array'],
            'salary_slips.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];
    }
}
