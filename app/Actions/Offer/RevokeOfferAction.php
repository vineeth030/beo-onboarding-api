<?php

namespace App\Actions\Offer;

use App\Enums\OfferStatus;
use App\Models\Activity;
use App\Models\Offer;
use App\Notifications\OfferRevokedNotification;

class RevokeOfferAction
{
    public function execute(Offer $offer, ?string $reason = null): void
    {
        $offer->load('employee.user');

        $offer->update(['status' => OfferStatus::OFFER_REVOKED]);

        $employee = $offer->employee;

        if ($employee && $employee->user) {
            $employee->user->notify(
                new OfferRevokedNotification(
                    employeeName: $employee->first_name.' '.$employee->last_name,
                    reason: $reason
                )
            );
        }

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'hr',
            'type' => 'update.offer.revoked',
            'title' => "Offer revoked for {$employee->full_name} by ".auth()->user()->name,
        ]);
    }
}
