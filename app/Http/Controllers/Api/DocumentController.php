<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Employee;
use App\Models\Document;

class DocumentController extends Controller
{
    public function index(Employee $employee)
    {
        return $employee->documents;
    }

    public function store(StoreDocumentRequest $request, Employee $employee)
    {
        $document = $employee->documents()->create($request->validated());
        return response()->json($document, 201);
    }

    public function show(Employee $employee, Document $document)
    {
        return $document;
    }

    public function update(UpdateDocumentRequest $request, Employee $employee, Document $document)
    {
        $document->update($request->validated());
        return response()->json($document);
    }

    public function destroy(Employee $employee, Document $document)
    {
        $document->delete();
        return response()->json(null, 204);
    }
}
