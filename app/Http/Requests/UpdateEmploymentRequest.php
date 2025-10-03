<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

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
            'employments' => ['required', 'array', 'min:1'],
            'employments.*.company_name' => ['required', 'string', 'max:255'],
            'employments.*.employee_id_at_company' => ['nullable', 'string', 'max:255'],
            'employments.*.designation' => ['required', 'string', 'max:255'],
            'employments.*.location' => ['required', 'string', 'max:255'],
            'employments.*.mode_of_employment' => ['required', 'string', 'max:255'],
            'employments.*.start_date' => ['required', 'date'],
            'employments.*.last_working_date' => ['nullable', 'date', 'after:employments.*.start_date'],
            'employments.*.resignation_acceptance_letter_file' => ['nullable'],
            'employments.*.experience_letter_file' => ['nullable'],
            'employments.*.is_current_org' => ['boolean'],
            'employments.*.is_serving_notice_period' => ['boolean'],
            'employments.*.salary_slips' => ['nullable', 'array'],
            'employments.*.salary_slips.*' => [
                'nullable', 
                function ($attribute, $value, $fail) {
                    // Case 1: Uploaded file
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $validator = Validator::make(
                            [$attribute => $value],
                            [$attribute => 'file|mimes:pdf,jpg,png|max:2048']
                        );
                        if ($validator->fails()) {
                            $fail($validator->errors()->first($attribute));
                        }
                    }
                    // Case 2: Existing string (URL or path)
                    elseif (is_string($value)) {
                        if (!str_starts_with($value, '/storage/')) {
                            $fail('The '.$attribute.' must be a valid file URL.');
                        }
                    }
                },
            ],
        ];
    }
}
