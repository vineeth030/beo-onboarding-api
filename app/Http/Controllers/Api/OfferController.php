<?php

namespace App\Http\Controllers\Api;

use App\Actions\Offer\AcceptOfferAction;
use App\Actions\Offer\DeclineOfferAction;
use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Mail\OfferLetterSendMail;
use App\Models\Activity;
use App\Models\Employee;
use App\Models\Offer;
use App\Notifications\OfferSendNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

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

        $employee = Employee::select(['id', 'user_id', 'first_name', 'last_name', 'email', 'joining_date', 'designation_id'])->where('id', $request->get('employee_id'))?->first();
        abort_if(! $employee, 404, 'Employee not found');

        $employee->update(['designation_id' => $request->get('designation_id')]);
        $offer->update(['status' => OfferStatus::PENDING]);

        // To refresh designation relationship.
        $employee->load(['designation:id,name', 'user:id,name,email']);
        abort_if(! $employee->user, 422, 'Employee has no associated user');
        abort_if(! $employee->designation, 422, 'Employee has no associated designation');

        $clientAndBEOEmails = array_merge($request->get('beo_emails'), $request->get('client_emails'));

        $this->sendOfferLetterEmailsToClientAndBeo($clientAndBEOEmails, $offer->email_attachment_content_for_client, $employee);
        $this->sendOfferLetterEmailToEmployee($employee->email, $offer->email_content_for_employee);

        $employee->user->notify(new OfferSendNotification);

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'hr',
            'type' => 'add.candidate',
            'title' => 'Offer created for '.$employee->fullname.' by '.auth()->user()->name,
        ]);

        return response()->json($offer, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Offer $offer)
    {
        $offer->load(['user', 'employee', 'client']);

        $offer->content = stripslashes($offer->content);

        return response()->json($offer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfferRequest $request, Offer $offer)
    {
        $offerData = $request->validated();

        // Accept offer
        if ($request->boolean('is_accepted')) {
            app(AcceptOfferAction::class)->execute(offer: $offer);

            return response()->json($offer);
        }

        // Decline offer
        if ($request->boolean('is_declined')) {
            app(DeclineOfferAction::class)->execute(offer: $offer);

            return response()->json($offer);
        }

        // Revert offer
        if ($request->boolean('is_revoked')) {
            $offer->update(['status' => OfferStatus::OFFER_REVOKED]);
        }

        // Update offer details
        if ($request->hasFile('sign_file_path')) {
            $path = $request->file('sign_file_path')->store("documents/{$offer->employee->employee_id}", 'public');
            $offerData['sign_file_path'] = $path;
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

    private function sendOfferLetterEmailsToClientAndBeo(array $emails, string $emailAttachmentContent, Employee $employee)
    {

        if (is_string($emailAttachmentContent)) {
            $htmlContent = stripslashes($emailAttachmentContent);
        } else {
            $htmlContent = '';
        }

        $html = view('pdf.offer-letter-template', ['htmlContent' => $htmlContent])->render();

        $offerLetterFileName = $employee->id.'-'.Str::random(8).'.pdf';
        $offerLetterFilePath = 'offer-letters/'.$employee->id.'/'.$offerLetterFileName;

        Storage::disk('public')->makeDirectory('offer-letters/'.$employee->id);

        Browsershot::html($html)
            ->setNodeBinary(env('NODE_BINARY_PATH'))
            ->setNpmBinary(env('NPM_BINARY_PATH'))
            ->noSandbox()->save(storage_path('app/public/'.$offerLetterFilePath));

        Mail::to($emails)->send(new OfferLetterSendMail(
            offerLetterFilePath: storage_path('app/public/'.$offerLetterFilePath),
            isClient: true, content: '', employee: $employee));
    }

    private function sendOfferLetterEmailToEmployee(string $email, string $offerLetterEmailContent)
    {

        if (is_string($offerLetterEmailContent) && str_starts_with($offerLetterEmailContent, '"')) {
            $offerLetterEmailContent = json_decode($offerLetterEmailContent, true);
        }

        if (is_string($offerLetterEmailContent)) {
            $htmlContent = stripslashes($offerLetterEmailContent);
        } else {
            $htmlContent = '';
        }

        $html = view('pdf.offer-letter-template', ['htmlContent' => $htmlContent])->render();

        Mail::to($email)->send(new OfferLetterSendMail(content: $html, employee: null));
    }
}
