<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\SyncDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    public function __construct(private DepartmentService $departmentService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Department::select('id', 'name', 'notice_period', 'is_family_insurance_paid_by_client')->with('emails')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        $validated = $request->validated();

        $result = $this->departmentService->createDepartment(
            [
                'name' => $validated['name'],
                'userIdCode' => $validated['userIdCode'],
                'notice_period' => $validated['notice_period'] ?? 0,
                'emails' => $validated['emails'] ?? [],
            ],
            $validated['sessionToken']
        );

        if (! $result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], $result['code']);
        }

        $department = Department::create([
            'id' => $validated['id'],
            'name' => $validated['name'],
            'notice_period' => $validated['notice_period'] ?? 0,
            'is_family_insurance_paid_by_client' => $validated['is_family_insurance_paid_by_client'] ?? false,
        ]);

        if (! empty($validated['emails'])) {
            $emails = collect($validated['emails'])->map(function ($email) {
                return ['email' => $email];
            });

            $department->emails()->createMany($emails);
        }

        return response()->json($department->load('emails'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        return $department->with('emails')->first();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $validated = $request->validated();

        $result = $this->departmentService->updateDepartment(
            $department->id,
            [
                'name' => $validated['name'],
                'userIdCode' => $validated['userIdCode'],
                'notice_period' => $validated['notice_period'] ?? $department->notice_period ?? 0,
                'emails' => $validated['emails'] ?? [],
            ],
            $validated['sessionToken']
        );

        if (! $result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], $result['code']);
        }

        $department->update([
            'name' => $validated['name'],
            'notice_period' => $validated['notice_period'] ?? $department->notice_period,
            'is_family_insurance_paid_by_client' => $validated['is_family_insurance_paid_by_client'] ?? $department->is_family_insurance_paid_by_client,
        ]);

        if ($request->has('emails')) {
            $department->emails()->delete();

            $emails = collect($validated['emails'])->map(function ($email) {
                return ['email' => $email];
            });

            $department->emails()->createMany($emails);
        }

        return response()->json($department->load('emails'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();

        return response()->json(null, 204);
    }

    /**
     * Sync departments from external API to local database.
     */
    public function sync(SyncDepartmentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $result = $this->departmentService->syncDepartments(
            $validated['userIdCode'],
            $validated['sessionToken']
        );

        if (! $result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], $result['code']);
        }

        $departments = $result['departments'];

        if (empty($departments)) {
            return response()->json([
                'message' => 'No departments found in external API.',
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

        DB::transaction(function () use ($departments, &$createdCount, &$updatedCount, &$failedCount) {
            foreach ($departments as $department) {
                try {
                     $model = Department::updateOrCreate(
                        ['id' => $department['group_id']],
                        [
                            'name' => $department['group_name'],
                            'notice_period' => $department['notice_period'] ?? 0,
                            'is_family_insurance_paid' => $department['is_family_insurance_paid_by_client'] ?? false
                        ]
                    );

                    // Sync emails
                    if (!empty($department['emails'])) {
                        $model->emails()->delete();
                        $emails = collect($department['emails'])->map(fn($email) => ['email' => $email]);
                        $model->emails()->createMany($emails);
                    }

                    if ($model->wasRecentlyCreated) {
                        $createdCount++;
                    } else {
                        $updatedCount++;
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error("Failed to sync department ID {$department['group_id']}: {$e->getMessage()}");
                }
            }
        });

        return response()->json([
            'message' => 'Departments synced successfully.',
            'stats' => [
                'total' => count($departments),
                'created' => $createdCount,
                'updated' => $updatedCount,
                'failed' => $failedCount,
            ],
        ]);
    }
}
