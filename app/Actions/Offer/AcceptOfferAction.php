<?php

namespace App\Actions\Offer;

use App\Enums\OfferStatus;
use App\Mail\OfferAcceptedMail;
use App\Mail\WelcomeCandidateMail;
use App\Models\Activity;
use App\Models\Offer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

class AcceptOfferAction
{
    public function execute(Offer $offer, string $acceptComment, UploadedFile $signFile): void
    {
        $path = $signFile->store("documents/{$offer->employee->employee_id}", 'public');

        $offer->update([
            'is_accepted' => true, 'status' => OfferStatus::ACCEPTED,
            'sign_file_path' => $path, 'comment' => $acceptComment,
        ]);

        Activity::create([
            'employee_id' => $offer->employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'candidate',
            'type' => 'accept.offer.candidate',
            'title' => 'Offer accepted by '.$offer->employee->full_name,
        ]);

        $clientEmailIds = $offer->client_emails;

        $hrEmailIds = $offer->beo_emails;

        // Send email notification to hr & client email ids.
        Mail::to($clientEmailIds)->cc($hrEmailIds)->send(new OfferAcceptedMail(employee: $offer->employee));

        if ($offer->employee->joining_date_type === \App\Enums\JoiningDateType::PRE_APPROVED) {
            Mail::to($offer->employee->email)
                ->cc([...$hrEmailIds, ...$clientEmailIds])
                ->send(new WelcomeCandidateMail(employee: $offer->employee));
        }
    }
}
