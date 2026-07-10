<?php

namespace App\Http\Controllers\Api;

use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Mail\SendDocumentsToAccountManagersMail;
use App\Models\Employee;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class EmployeeOnboardingController extends Controller
{
    public function onboard(Employee $employee): Response
    {
        $employee->activeOffer()->update([
            'status' => OfferStatus::REGISTERED_EMPLOYEE,
        ]);

        $employee->update(['is_onboarded' => 1]);

        $acManagerEmails = config('app.accounting_manager_emails', []);

        if (! empty($acManagerEmails) && $employee->documents()->exists()) {
            Mail::to($acManagerEmails)->send(new SendDocumentsToAccountManagersMail($employee));
        }

        return response()->noContent();
    }
}
