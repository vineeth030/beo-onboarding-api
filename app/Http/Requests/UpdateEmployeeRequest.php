<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
        $employeeId = $this->route('employee')->id;

        return [
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'middle_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'fathers_name' => ['sometimes', 'required', 'string', 'max:255'],
            'file' => ['sometimes', 'required', 'file', 'mimes:pdf,jpg,png', 'max:2048'],
            'department_id' => ['sometimes', 'required'],
            'office_id' => ['sometimes', 'nullable', 'integer'],
            'dob' => ['sometimes', 'required', 'date'],
            'gender' => ['sometimes', 'required', 'string'],
            'marital_status' => ['sometimes', 'required', 'string'],
            'nationality' => ['sometimes', 'required', 'integer'],
            'place_of_birth' => ['sometimes', 'required', 'string'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:employees,email,' . $employeeId],
            'mobile' => ['sometimes', 'required', 'string', 'max:255', 'unique:employees,mobile,' . $employeeId],
            'status' => ['sometimes', 'integer', 'in:0,1,2,3,4'],
            'offer_letter_status' => ['sometimes', 'integer', 'in:0,1,2'],
            'division' => ['sometimes', 'integer', 'in:0,1'],
            'category' => ['sometimes', 'integer'],
            'joining_date' => ['sometimes', 'nullable', 'string'],
            'is_verified' => ['sometimes','boolean'],
            'is_open' => ['sometimes','boolean'],
            'is_pre_joining_completed' => ['sometimes','boolean'],
            'is_joining_date_update_approved' => ['sometimes', 'nullable', 'integer'],
            'updated_joining_date' => ['sometimes', 'date'],
            'requested_joining_date' => ['sometimes', 'nullable', 'date'],
            'buddy_id' => ['sometimes', 'integer'],
            'poc_1_id' => ['sometimes', 'integer'],
            'poc_2_id' => ['sometimes', 'integer'],
            'department_id' => ['sometimes', 'nullable', 'integer'],
            'designation_id' => ['sometimes', 'integer'],
        ];
    }
}
