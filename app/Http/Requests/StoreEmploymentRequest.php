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
            'employments' => ['required', 'array', 'min:1'],
            'employments.*.company_name' => ['required', 'string', 'max:255'],
            'employments.*.employee_id_at_company' => ['nullable', 'string', 'max:255'],
            'employments.*.designation' => ['required', 'string', 'max:255'],
            'employments.*.location' => ['required', 'string', 'max:255'],
            'employments.*.mode_of_employment' => ['required', 'string', 'max:255'],
            'employments.*.start_date' => ['required', 'date'],
            'employments.*.last_working_date' => ['nullable', 'date', 'after:employments.*.start_date'],
            'employments.*.resignation_acceptance_letter_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'employments.*.experience_letter_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'employments.*.is_current_org' => ['boolean'],
            'employments.*.is_serving_notice_period' => ['boolean'],
            'employments.*.salary_slips' => ['nullable', 'array'],
            'employments.*.salary_slips.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];
    }
}
