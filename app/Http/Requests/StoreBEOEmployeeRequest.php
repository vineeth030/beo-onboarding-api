<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBEOEmployeeRequest extends FormRequest
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
            'user_id_code' => ['required', 'integer'],
            'user_id' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'country_id' => ['required', 'integer'],
            'communication_address_line_1' => ['required', 'string', 'max:255'],
            'communication_address_line_2' => ['nullable', 'string', 'max:255'],
            'communication_address_district' => ['required', 'string', 'max:255'],
            'communication_address_pin_code' => ['required', 'integer'],
            'communication_address_state' => ['required', 'string', 'max:255'],
            'communication_address_country_id' => ['required', 'integer'],
            'mobile' => ['required', 'string', 'max:255'],
            'permanent_address_same_as_communication' => ['required', 'boolean'],
            'permanent_address_line_1' => ['required', 'string', 'max:255'],
            'permanent_address_line_2' => ['nullable', 'string', 'max:255'],            
            'permanent_address_district' => ['required', 'string', 'max:255'],
            'permanent_address_pin_code' => ['required', 'string', 'max:255'],
            'permanent_address_state' => ['required', 'string', 'max:255'],
            'permanent_address_country_id' => ['required', 'integer'],
            'email_id' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'confirm_password' => ['required', 'string', 'max:255'],
            'preferred_language' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'integer'],
            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', 'string'],
            'designation_id' => ['required', 'integer'],
            'group_id' => ['required', 'integer'],
            'date_of_joining' => ['required', 'date'],
            'floor_id' => ['required', 'integer'],
            'blood_group' => ['required', 'string', 'max:255'],
            'blood_group_id' => ['required', 'integer'],
            't_shirt_size' => ['required', 'string', 'max:255'],
        ];
    }
}
