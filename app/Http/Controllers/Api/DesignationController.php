<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDesignationRequest;
use App\Http\Requests\SyncDesignationRequest;
use App\Http\Requests\UpdateDesignationRequest;
use App\Models\Designation;
use App\Services\DesignationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DesignationController extends Controller
{
    public function __construct(private DesignationService $designationService) {}

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
        $validated = $request->validated();

        $result = $this->designationService->createDesignation(
            [
                'name' => $validated['name'],
                'userIdCode' => $validated['userIdCode'],
                'companyID' => 3, // BEO India
            ],
            $validated['sessionToken']
        );

        if (! $result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], $result['code']);
        }

        $designation = Designation::create([
            'name' => $validated['name'],
        ]);

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
        $validated = $request->validated();

        $result = $this->designationService->updateDesignation(
            $designation->id,
            [
                'name' => $validated['name'],
                'userIdCode' => $validated['userIdCode'],
                'companyID' => 3,// BEO India
            ],
            $validated['sessionToken']
        );

        if (! $result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], $result['code']);
        }

        $designation->update([
            'name' => $validated['name'],
        ]);

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

    /**
     * Sync designations from external API to local database.
     */
    public function sync(SyncDesignationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->designationService->syncDesignations(
            $validated['userIdCode'],
            $validated['sessionToken']
        );

        if (! $result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], $result['code']);
        }

        $designations = $result['designations'];

        if (empty($designations)) {
            return response()->json([
                'message' => 'No designations found in external API.',
                'stats' => [
                    'total' => 0,
                    'created' => 0,
                    'updated' => 0,
                    'failed' => 0,
                ],
            ]);
        }

        $createdCount = 0;
        $updatedCount = 0;
        $failedCount = 0;

        DB::transaction(function () use ($designations, &$createdCount, &$updatedCount, &$failedCount) {
            foreach ($designations as $designation) {
                try {
                    $model = Designation::updateOrCreate(
                        ['id' => $designation['dId']],
                        ['name' => $designation['designation']]
                    );

                    if ($model->wasRecentlyCreated) {
                        $createdCount++;
                    } else {
                        $updatedCount++;
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error("Failed to sync designation ID {$designation['dId']}: {$e->getMessage()}");
                }
            }
        });

        return response()->json([
            'message' => 'Designations synced successfully.',
            'stats' => [
                'total' => count($designations),
                'created' => $createdCount,
                'updated' => $updatedCount,
                'failed' => $failedCount,
            ],
        ]);
    }
}
