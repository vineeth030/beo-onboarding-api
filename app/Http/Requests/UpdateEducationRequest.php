<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class UpdateEducationRequest extends FormRequest
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
            'educations' => ['required', 'array', 'min:1', 'max:25'],
            'educations.*.title' => ['required', 'string', 'max:255'],
            'educations.*.board' => ['required', 'string', 'max:255'],
            'educations.*.school' => ['required', 'string', 'max:255'],
            'educations.*.specialization' => ['nullable', 'string', 'max:255'],
            'educations.*.percentage' => ['required', 'string', 'max:255'],
            'educations.*.from_date' => ['sometimes', 'date'],
            'educations.*.to_date' => ['sometimes','date'],
            'educations.*.mode_of_education' => ['nullable', 'string', 'max:255'],
            'educations.*.is_highest' => ['boolean'],
            'educations.*.file' => [
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
            ]
        ];
    }
}
