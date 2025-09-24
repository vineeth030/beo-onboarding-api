<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
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
            'addresses' => ['required', 'array', 'size:2'],
            'addresses.*.line1' => ['required', 'string', 'max:255'],
            'addresses.*.line2' => ['nullable', 'string', 'max:255'],
            'addresses.*.line3' => ['nullable', 'string', 'max:255'],
            'addresses.*.landmark' => ['nullable', 'string', 'max:255'],
            'addresses.*.country' => ['required', 'string', 'max:255'],
            'addresses.*.state' => ['required', 'string', 'max:255'],
            'addresses.*.city' => ['nullable', 'string', 'max:255'],
            'addresses.*.pin' => ['required', 'string', 'max:255'],
            'addresses.*.duration_of_stay' => ['required', 'string', 'max:255'],
            'addresses.*.type' => ['required', 'in:current,permanent'],
        ];
    }
}
