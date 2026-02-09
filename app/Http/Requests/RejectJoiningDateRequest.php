<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectJoiningDateRequest extends FormRequest
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
            'updated_joining_date' => ['required', 'date'],
            'requested_joining_date' => ['required', 'date'],
        ];
    }
}
