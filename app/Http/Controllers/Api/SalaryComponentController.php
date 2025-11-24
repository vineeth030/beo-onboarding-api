<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalaryComponentController extends Controller
{
    public function index()
    {
        return DB::table('salary_components')->first();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'basic_percentage' => 'required|numeric',
            'da_percentage' => 'required|numeric',
            'hra_percentage' => 'required|numeric',
            'travel_allowance_percentage' => 'required|numeric',
            'communication_allowance_threshold' => 'required|numeric',
            'communication_allowance_amount' => 'required|numeric',
            'research_allowance_threshold' => 'required|numeric',
            'research_allowance_amount' => 'required|numeric',
            'insurance_internal' => 'required|numeric',
            'insurance_external' => 'required|numeric',
            'employer_pf_annual' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        DB::table('salary_components')->insert([
            'basic_percentage' => $request->basic_percentage,
            'da_percentage' => $request->da_percentage,
            'hra_percentage' => $request->hra_percentage,    
            'travel_allowance_percentage' => $request->travel_allowance_percentage,
            'communication_allowance_threshold' => $request->communication_allowance_threshold,
            'communication_allowance_amount' => $request->communication_allowance_amount,
            'research_allowance_threshold' => $request->research_allowance_threshold,
            'research_allowance_amount' => $request->research_allowance_amount,
            'insurance_internal' => $request->insurance_internal,    
            'insurance_external' => $request->insurance_external,
            'employer_pf_annual' => $request->employer_pf_annual
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Salary components saved successfully.'
        ], 201);
    }
}
