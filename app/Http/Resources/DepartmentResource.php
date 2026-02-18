<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'notice_period' => $this->notice_period,
            'is_family_insurance_paid_by_client' => $this->is_family_insurance_paid_by_client,
            'is_support_staff_required' => $this->is_support_staff_required,
            'is_outsource' => $this->is_outsource,
            'is_single_swipe' => $this->is_single_swipe,
            'emails' => $this->emails->pluck('email'),
        ];
    }
}
