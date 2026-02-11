<?php

namespace App\Actions\Offer;

use App\Enums\OfferStatus;
use App\Mail\OfferDeclinedMail;
use App\Models\Activity;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

class DeclineOfferAction
{
    public function execute(Offer $offer, String $declineReason): void
    {
        $offer->update(['status' => OfferStatus::REJECTED, 'decline_reason' => $declineReason]);

        Activity::create([
            'employee_id' => $offer->employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'candidate',
            'type' => 'decline.offer.candidate',
            'title' => 'Offer declined by '.$offer->employee->name,
        ]);

        $hrEmailIds = config('app.hr_emails');

        // Send email notification to hr & client email ids.
        Mail::to($hrEmailIds)->send(new OfferDeclinedMail(employee: $offer->employee));
    }
}
