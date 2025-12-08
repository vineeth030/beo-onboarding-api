<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDesignationRequest;
use App\Http\Requests\UpdateDesignationRequest;
use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'sessionToken' => 'required|string',
            'userIdCode'   => 'required|numeric',
        ]);

        return (new BEOSystemController)->designations($validated['sessionToken'], $validated['userIdCode']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDesignationRequest $request)
    {
        $designation = Designation::create($request->validated());

        return response()->json($designation, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Designation $designation)
    {
        return $designation;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDesignationRequest $request, Designation $designation)
    {
        $designation->update($request->validated());

        return response()->json($designation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Designation $designation)
    {
        $designation->delete();

        return response()->json(null, 204);
    }
}
