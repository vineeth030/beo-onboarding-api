<?php

namespace App\Http\Requests;

use Illuminate_Foundation_Http_FormRequest;

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
            'designation' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'mode_of_employment' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'is_current_org' => ['boolean'],
        ];
    }
}
