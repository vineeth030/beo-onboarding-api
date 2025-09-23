<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentRequest extends FormRequest
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
            'type' => ['nullable', 'required', 'string', 'in:pan,aadhar,passport,driving_license,voter_id'],
            'number' => ['nullable', 'required', 'string', 'max:255'],
            'name_on_doc' => ['nullable', 'required', 'string', 'max:255'],
            'file' => ['nullable', 'required', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
        ];
    }
}
