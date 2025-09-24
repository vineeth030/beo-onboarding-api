<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEducationRequest extends FormRequest
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
            'educations' => ['required', 'array', 'min:1', 'max:4'],
            'educations.*.title' => ['required', 'string', 'max:255'],
            'educations.*.board' => ['required', 'string', 'max:255'],
            'educations.*.school' => ['required', 'string', 'max:255'],
            'educations.*.specialization' => ['nullable', 'string', 'max:255'],
            'educations.*.percentage' => ['required', 'string', 'max:255'],
            'educations.*.from_date' => ['required', 'date'],
            'educations.*.to_date' => ['required', 'date'],
            'educations.*.mode_of_education' => ['nullable', 'string', 'max:255'],
            'educations.*.is_highest' => ['boolean'],
            'educations.*.file' => ['required', 'file', 'mimes:pdf,jpg,png', 'max:2048']
        ];
    }
}
