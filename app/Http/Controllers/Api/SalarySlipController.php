<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employment;
use App\Models\SalarySlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SalarySlipController extends Controller
{
    public function index(Employment $employment)
    {
        return $employment->salarySlips;
    }

    public function store(Request $request, Employment $employment)
    {
        $request->validate([
            'salary_slip' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $filePath = $request->file('salary_slip')->storeAs(
            'salary_slips',
            uniqid() . '.' . $request->file('salary_slip')->getClientOriginalExtension(),
            'public'
        );

        $salarySlip = $employment->salarySlips()->create([
            'file_path' => $filePath,
            'preview_url' => Storage::url($filePath),
        ]);

        return response()->json($salarySlip, 201);
    }

    public function show(Employment $employment, SalarySlip $salarySlip)
    {
        return $salarySlip;
    }

    public function destroy(Employment $employment, SalarySlip $salarySlip)
    {
        // Delete the file from storage
        if ($salarySlip->file_path) {
            Storage::disk('public')->delete($salarySlip->file_path);
        }

        $salarySlip->delete();
        return response()->json(null, 204);
    }
}
