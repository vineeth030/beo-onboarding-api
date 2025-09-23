<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmploymentRequest extends FormRequest
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
            'company_name' => ['required', 'string', 'max:255'],
            'employee_id_at_company' => ['nullable', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'mode_of_employment' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'last_working_date' => ['nullable', 'date', 'after:start_date'],
            'resignation_acceptance_letter_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'experience_letter_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'is_current_org' => ['boolean'],
            'salary_slips' => ['nullable', 'array'],
            'salary_slips.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];
    }
}
