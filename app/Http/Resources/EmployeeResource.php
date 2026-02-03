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
            'password' => $this->password,
            'status' => $this->status,
            'offer_status' => $this->activeOffer?->status?->value ?? 0,
            'mobile' => $this->mobile,
            'category' => $this->category,
            'division' => $this->division,
            'joining_date' => $this->joining_date,
            'department_id' => $this->department_id,
            'department_name' => $this->department?->name,
            'updated_joining_date' => $this->updated_joining_date,
            'is_joining_date_update_approved' => $this->is_joining_date_update_approved,
            'requested_joining_date' => $this->requested_joining_date,
            'created_at' => $this->created_at,
            'office' => $this->whenLoaded('office'),
            'addresses' => $this->whenLoaded('addresses'),
            'documents' => $this->whenLoaded('documents'),
            'educations' => $this->whenLoaded('educations'),
            'employments' => $this->whenLoaded('employments'),
            'offers' => $this->whenLoaded('offers'),
        ];
    }
}
