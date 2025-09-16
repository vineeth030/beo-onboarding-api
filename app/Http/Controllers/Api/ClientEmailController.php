<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientEmailRequest;
use App\Http\Requests\UpdateClientEmailRequest;
use App\Models\ClientEmail;

class ClientEmailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ClientEmail::with('client')->orderBy('id', 'desc')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientEmailRequest $request)
    {
        $clientEmail = ClientEmail::create($request->validated());
        return response()->json($clientEmail, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ClientEmail $clientEmail)
    {
        return $clientEmail->load('client');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientEmailRequest $request, ClientEmail $clientEmail)
    {
        $clientEmail->update($request->validated());
        return response()->json($clientEmail);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClientEmail $clientEmail)
    {
        $clientEmail->delete();
        return response()->json(null, 204);
    }
}
