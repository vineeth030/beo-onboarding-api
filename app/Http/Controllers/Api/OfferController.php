<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Mail\OfferLetterSend;
use App\Models\Employee;
use App\Models\Offer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        if ($request->has('emails')) {
            $emails = collect($request->input('emails'))->map(function ($email) {
                return ['email' => $email];
            });

            $rawContent = $offer->content;

            if (is_string($rawContent) && str_starts_with($rawContent, '"')) {
                $rawContent = json_decode($rawContent, true);
            }

            if (is_string($rawContent)) {
                $htmlContent = stripslashes($rawContent);
            } else {
                $htmlContent = '';
            }

            $offerLetterFileName = $employee->id . '-' . Str::random(8) . '.pdf';
            $offerLetterFilePath = 'offer-letters/' . $employee->id . '/' . $offerLetterFileName;

            Storage::disk('public')->makeDirectory('offer-letters/' . $employee->id);

            Log::info('The HTML Content: ' . $htmlContent);

            //$htmlContent = "<html><head><meta charset=\"UTF-8\" /><meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" /><script src=\"https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4\"></script></head><body><div class=\"p-10\">" . $htmlContent . "</div></body></html>";

            $html = view('pdf.offer-letter-template', [
                'htmlContent' => $htmlContent
            ])->render();

            Browsershot::html($html)
                ->setNodeBinary(env('NODE_BINARY_PATH'))
                ->setNpmBinary(env('NPM_BINARY_PATH'))
                ->noSandbox()->save(storage_path('app/public/' . $offerLetterFilePath));

            // Send offer letter to these email ids $emails.
            //Mail::to($emails)->send(new OfferLetterSend(storage_path('app/public/' . $offerLetterFilePath)));
        }

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
        $offer->update($request->validated());
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
}