<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Employee;
use App\Models\Document;
use Illuminate\Support\Arr;

class DocumentController extends Controller
{
    public function index(Employee $employee)
    {
        return $employee->documents;
    }

    public function store(StoreDocumentRequest $request, Employee $employee)
    {
        $path = $request->file('file')->store("documents/{$employee->id}", 'public');

        $document = $employee->documents()->create(Arr::except($request->validated(), ['file']) + ['file_path' => '/storage/' . $path]);

        return response()->json($document, 201);
    }

    public function show(Employee $employee, Document $document)
    {
        return $document;
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store("documents/{$document->employee_id}", 'public');
            $document->update(Arr::except($request->validated(), ['file']) + ['file_path' => '/storage/' . $path]);
        }else{
            $document->update(Arr::except($request->validated(), ['file']));
        }

        
        return response()->json($document);
    }

    public function destroy(Employee $employee, Document $document)
    {
        $document->delete();
        return response()->json(null, 204);
    }
}
