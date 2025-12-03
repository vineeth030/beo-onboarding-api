<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource with optional filters.
     */
    public function index(Request $request)
    {
        $query = Activity::query()->with([
            'employee:id,first_name,last_name',
            'performedBy:id,name'
        ])
        ->select('id', 'title', 'employee_id', 'performed_by_user_id', 'created_at');

        // Filter by employee_id
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by user_type
        if ($request->has('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // Order by most recent first
        $query->orderBy('id', 'desc');

        $activities = $query->paginate($request->get('per_page', 15));

        $activities->getCollection()->transform(function ($activity) {
            return [
                'id'              => $activity->id,
                'title'           => $activity->title,
                //'candidate'       => $activity->employee ? $activity->employee->first_name . " " . $activity->employee->last_name : null,
                //'performed_by'    => $activity->performedBy ? $activity->performedBy->name : null, 
                'date'            => $activity->created_at->toDateTimeString(),
            ];
        });

        return $activities;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request)
    {
        $activity = Activity::create($request->validated());

        return response()->json($activity->load(['employee', 'performedBy']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        return $activity->load(['employee', 'performedBy']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $activity->update($request->validated());

        return response()->json($activity->load(['employee', 'performedBy']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();
        return response()->json(null, 204);
    }
}
