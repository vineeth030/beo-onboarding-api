<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Employee;
use App\Models\Offer;

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

        Employee::where('id', $request->get('employee_id'))?->update(['offer_letter_status' => 1]);

        if ($request->has('emails')) {
            $emails = collect($request->input('emails'))->map(function ($email) {
                return ['email' => $email];
            });

            // Send offer letter to these email ids $emails.
        }

        return response()->json($offer, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Offer $offer)
    {
        return $offer->load(['user', 'employee', 'client']);
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