<?php

namespace App\Actions\Offer;

use App\Models\Activity;
use App\Models\Offer;

class DeclineOfferAction 
{
    public function execute(Offer $offer) : void 
    {    
        $offer->employee()->update(['offer_letter_status' => 3]);

        Activity::create([
            'employee_id' => $offer->employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'candidate',
            'type' => 'decline.offer.candidate',
            'title' => 'Offer declined by ' . $offer->employee->name,
        ]);
    }
}