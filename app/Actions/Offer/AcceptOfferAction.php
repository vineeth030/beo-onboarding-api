<?php

namespace App\Actions\Offer;

use App\Mail\OfferAcceptedMail;
use App\Models\Activity;
use App\Models\Offer;
use App\Models\User;
use App\Notifications\OfferAcceptNotification;
use Illuminate\Support\Facades\Mail;

class AcceptOfferAction 
{
    public function execute(Offer $offer) : void 
    {    
        $offer->employee()->update(['offer_letter_status' => 2]);

        Activity::create([
            'employee_id' => $offer->employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'candidate',
            'type' => 'accept.offer.candidate',
            'title' => 'Offer accepted by ' . $offer->employee->name,
        ]);

        $clientEmailIds = $offer->employee?->department?->emails->pluck('email')->toArray();

        $hrEmailIds = User::where('role', 'admin')->pluck('email')->toArray();

        //Send email notification to hr & client email ids.
        Mail::to([...$hrEmailIds, ...$clientEmailIds])->send(new OfferAcceptedMail(employee: $offer->employee));
    }
}