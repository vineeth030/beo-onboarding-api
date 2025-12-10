<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Mail\OfferLetterSend;
use App\Models\Activity;
use App\Models\Employee;
use App\Models\Offer;
use App\Notifications\OfferCreated;
use App\Notifications\OfferSendNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Str;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Offer::with(['user', 'employee', 'client'])->orderBy('id', 'desc')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOfferRequest $request)
    {
        $offer = Offer::create($request->validated());

        $employee = Employee::where('id', $request->get('employee_id'))?->first();
        $employee->update(['offer_letter_status' => 1]);

        $clientAndBEOEmails = array_merge($request->get('beo_emails'), $request->get('client_emails'));

        $this->sendOfferLetterEmailsToClientAndBeo($clientAndBEOEmails, $offer->email_attachment_content_for_client, $employee->id);
        $this->sendOfferLetterEmailToEmployee($employee->email, $offer->email_content_for_employee);

        $employee->notify(new OfferSendNotification());

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'hr',
            'type' => 'add.candidate',
            'title' => 'Offer created for ' . $employee->name . ' by ' . auth()->user()->name,
        ]);

        return response()->json($offer, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Offer $offer)
    {
        $offer->load(['user', 'employee', 'client']);

        Log::info('Content: ', [stripslashes($offer->content)]);

        $offer->content = stripslashes($offer->content);

        return response()->json($offer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        $offerData = $request->validated();

        if ($request->hasFile('sign_file_path')) {
            $path = $request->file('sign_file_path')->store("documents/{$offer->employee->employee_id}", 'public');
            $offerData['sign_file_path'] = $path;   
        }

        if ($request->boolean('is_accepted')) {
            $offer->employee()->update(['offer_letter_status' => 2]);

            Activity::create([
                'employee_id' => $offer->employee->id,
                'performed_by_user_id' => auth()->user()->id,
                'user_type' => 'candidate',
                'type' => 'accept.offer.candidate',
                'title' => 'Offer accepted by ' . $offer->employee->name,
            ]);

        } elseif ($request->boolean('is_declined')) {
            $offer->employee()->update(['offer_letter_status' => 3]);

            Activity::create([
                'employee_id' => $offer->employee->id,
                'performed_by_user_id' => auth()->user()->id,
                'user_type' => 'candidate',
                'type' => 'decline.offer.candidate',
                'title' => 'Offer declined by ' . $offer->employee->name,
            ]);
        }

        $offer->update($offerData);

        return response()->json($offer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Offer $offer)
    {
        $offer->delete();
        return response()->json(null, 204);
    }

    private function sendOfferLetterEmailsToClientAndBeo(array $emails, string $emailAttachmentContent, int $employee_id) {

        if (is_string($emailAttachmentContent) && str_starts_with($emailAttachmentContent, '"')) {
            $offerLetterEmailContent = json_decode($emailAttachmentContent, true);
        }

        if (is_string($emailAttachmentContent)) {
            $htmlContent = stripslashes($emailAttachmentContent);
        } else {
            $htmlContent = '';
        }

        $html = view('pdf.offer-letter-template', ['htmlContent' => $htmlContent])->render();

        $offerLetterFileName = $employee_id . '-' . Str::random(8) . '.pdf';
        $offerLetterFilePath = 'offer-letters/' . $employee_id . '/' . $offerLetterFileName;

        Storage::disk('public')->makeDirectory('offer-letters/' . $employee_id);

        Browsershot::html($html)
            ->setNodeBinary(env('NODE_BINARY_PATH'))
            ->setNpmBinary(env('NPM_BINARY_PATH'))
            ->noSandbox()->save(storage_path('app/public/' . $offerLetterFilePath));

        //Mail::to($emails)->send(new OfferLetterSend(storage_path('app/public/' . $offerLetterFilePath), true, ""));
    }

    private function sendOfferLetterEmailToEmployee(string $email, string $offerLetterEmailContent) {

        if (is_string($offerLetterEmailContent) && str_starts_with($offerLetterEmailContent, '"')) {
            $offerLetterEmailContent = json_decode($offerLetterEmailContent, true);
        }

        if (is_string($offerLetterEmailContent)) {
            $htmlContent = stripslashes($offerLetterEmailContent);
        } else {
            $htmlContent = '';
        }

        $html = view('pdf.offer-letter-template', ['htmlContent' => $htmlContent])->render();

        Mail::to($email)->send(new OfferLetterSend(content: $html));
    }
}