<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'email' => $this->email,
            'fathers_name' => $this->fathers_name,
            'dob' => $this->dob,
            'nationality' => $this->nationality,
            'place_of_birth' => $this->place_of_birth,
            'gender' => $this->gender,
            'marital_status' => $this->marital_status,
            'blood_group' => $this->blood_group,
            'photo_path' => $this->photo_path,
            'password' => $this->password,
            'status' => $this->status,
            'offer_status' => $this->offers->last()->status?->value ?? 0,
            'mobile' => $this->mobile,
            'category' => $this->category,
            'division' => $this->division,
            'joining_date' => $this->joining_date,
            'department_id' => $this->department_id,
            'department_name' => $this->department?->name,
            'updated_joining_date' => $this->updated_joining_date,
            'is_open' => $this->is_open,
            'is_joining_date_update_approved' => $this->is_joining_date_update_approved,
            'is_pre_joining_form_downloaded' => $this->is_pre_joining_form_downloaded,
            'is_onboarded' => $this->is_onboarded,
            'requested_joining_date' => $this->requested_joining_date,
            'created_at' => $this->created_at,
            'buddy_id' => $this->buddy_id,
            'poc_1_id' => $this->poc_1_id,
            'poc_2_id' => $this->poc_2_id,
            'office_id' => $this->office_id,
            'office' => $this->whenLoaded('office'),
            'addresses' => $this->whenLoaded('addresses'),
            'documents' => $this->whenLoaded('documents'),
            'educations' => $this->whenLoaded('educations'),
            'employments' => $this->whenLoaded('employments'),
            'offers' => $this->whenLoaded('offers'),
        ];
    }
}
