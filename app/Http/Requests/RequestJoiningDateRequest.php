<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestJoiningDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'requested_joining_date' => ['required', 'date'],
        ];
    }
}
