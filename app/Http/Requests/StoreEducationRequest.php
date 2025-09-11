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
            'board' => ['required', 'string', 'max:255'],
            'school' => ['required', 'string', 'max:255'],
            'specialization' => ['required', 'string', 'max:255'],
            'percentage' => ['required', 'string', 'max:255'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date'],
            'mode_of_education' => ['required', 'string', 'max:255'],
            'certificate_path' => ['required', 'string', 'max:255'],
            'is_highest' => ['boolean'],
        ];
    }
}
