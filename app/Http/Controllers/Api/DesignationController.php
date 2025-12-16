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
        return response()->json(Designation::select('id', 'name')->get());
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
        return $designation->load('emails');
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
        $designation->emails()->delete();
        $designation->delete();

        return response()->json(null, 204);
    }
}
