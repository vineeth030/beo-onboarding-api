<?php

namespace App\Http\Controllers\Api;

use App\Actions\Employee\NotifyCandidateOnReopenAction;
use App\Actions\Employee\NotifyHrOnResubmissionAction;
use App\Enums\ResubmissionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class DocumentController extends Controller
{
    public function index(Employee $employee)
    {
        Gate::authorize('view', $employee);

        return $employee->documents;
    }

    public function store(StoreDocumentRequest $request, Employee $employee)
    {
        Gate::authorize('update', $employee);
        $path = $request->file('file')->store("documents/{$employee->id}", 'public');

        $document = $employee->documents()->create(Arr::except($request->validated(), ['file']) + ['file_path' => '/storage/'.$path]);

        return response()->json($document, 201);
    }

    public function show(Employee $employee, Document $document)
    {
        Gate::authorize('view', $employee);

        return $document;
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        Gate::authorize('update', $document->employee);
        $wasOpenForChanges = $document->is_open == 1;

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store("documents/{$document->employee_id}", 'public');
            $document->update(Arr::except($request->validated(), ['file']) + ['file_path' => '/storage/'.$path]);
        } else {
            $document->update(Arr::except($request->validated(), ['file']));
        }

        if ($wasOpenForChanges && auth()->user()->role == 'candidate') {
            $document->update(['is_open' => 0]);
            app(NotifyHrOnResubmissionAction::class)->execute(
                employee: $document->employee,
                type: ResubmissionType::Document
            );
        }

        return response()->json($document);
    }

    public function destroy(Employee $employee, Document $document)
    {
        Gate::authorize('update', $employee);

        $document->delete();

        return response()->json(null, 204);
    }

    public function open(Document $document): JsonResponse
    {
        Gate::authorize('adminOnly', Employee::class);

        $document->update(['is_open' => 1]);

        app(NotifyCandidateOnReopenAction::class)->execute(
            employee: $document->employee,
            type: ResubmissionType::Document
        );

        return response()->json(null, 200);
    }
}
