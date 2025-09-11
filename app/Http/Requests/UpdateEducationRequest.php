<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'board' => ['sometimes', 'required', 'string', 'max:255'],
            'school' => ['sometimes', 'required', 'string', 'max:255'],
            'specialization' => ['sometimes', 'required', 'string', 'max:255'],
            'percentage' => ['sometimes', 'required', 'string', 'max:255'],
            'from_date' => ['sometimes', 'required', 'date'],
            'to_date' => ['sometimes', 'required', 'date'],
            'mode_of_education' => ['sometimes', 'required', 'string', 'max:255'],
            'certificate_path' => ['sometimes', 'required', 'string', 'max:255'],
            'is_highest' => ['sometimes', 'boolean'],
        ];
    }
}
