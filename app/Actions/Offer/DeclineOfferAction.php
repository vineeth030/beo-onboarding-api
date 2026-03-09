<?php

namespace App\Actions\Offer;

use App\Enums\OfferStatus;
use App\Mail\OfferDeclinedMail;
use App\Models\Activity;
use App\Models\Offer;
use Illuminate\Support\Facades\Mail;

class DeclineOfferAction
{
    public function execute(Offer $offer, String $declineReason, string $name): void
    {
        $offer->update([
            'status' => OfferStatus::REJECTED, 'decline_reason' => $declineReason, 'is_declined' => 1, 'name' => $name
        ]);

        Activity::create([
            'employee_id' => $offer->employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'candidate',
            'type' => 'decline.offer.candidate',
            'title' => 'Offer declined by '.$offer->employee->full_name,
        ]);

        $hrEmailIds = $offer->beo_emails;

        // Send email notification to hr & client email ids.
        Mail::to($hrEmailIds)->send(new OfferDeclinedMail(employee: $offer->employee));
    }
}
