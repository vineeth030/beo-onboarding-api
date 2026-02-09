<?php

namespace App\Http\Controllers\Api;

use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Response;

class EmployeeOnboardingController extends Controller
{
    public function onboard(Employee $employee): Response
    {
        $employee->activeOffer()->update([
            'status' => OfferStatus::REGISTERED_EMPLOYEE,
        ]);

        $employee->update(['is_onboarded' => 1]);

        return response()->noContent();
    }
}
