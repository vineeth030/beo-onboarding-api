<?php

namespace App\Actions\Offer;

use App\Enums\OfferStatus;
use App\Mail\OfferAcceptanceAcknowledgementMail;
use App\Mail\OfferAcceptedMail;
use App\Models\Activity;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AcceptOfferAction
{
    public function execute(Offer $offer): void
    {
        $offer->update(['status' => OfferStatus::ACCEPTED]);

        Activity::create([
            'employee_id' => $offer->employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'candidate',
            'type' => 'accept.offer.candidate',
            'title' => 'Offer accepted by '.$offer->employee->name,
        ]);

        $clientEmailIds = $offer->employee?->department?->emails->pluck('email')->toArray();

        $hrEmailIds = User::where('role', 'admin')->pluck('email')->toArray();

        // Send email notification to hr & client email ids.
        Mail::to([...$hrEmailIds, ...$clientEmailIds])->send(new OfferAcceptedMail(employee: $offer->employee));

        Mail::to($offer->employee->email)
            ->cc([...$hrEmailIds, ...$clientEmailIds])
            ->send(new OfferAcceptanceAcknowledgementMail(employee: $offer->employee));
    }
}
