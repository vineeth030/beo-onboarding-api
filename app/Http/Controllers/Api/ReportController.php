<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Offer;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from_date' => 'sometimes|date',
            'to_date' => 'sometimes|date'    
        ]);

        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $offerQuery = Offer::query();
        $employeeQuery = Employee::query();

        if ($from_date && $to_date) {
            $offerQuery->whereBetween('created_at', [$from_date, $to_date]);
            $employeeQuery->whereBetween('created_at', [$from_date, $to_date]);
        }

        $total_offers = $offerQuery->count();
        $accepted_offers = (clone $offerQuery)->where('is_accepted', 1)->count();
        $pending_offers = (clone $offerQuery)->where('is_accepted', 0)->where('is_declined', 0)->count();
        $declined_offers = (clone $offerQuery)->where('is_declined', 1)->count();

        $background_verification_pending = (clone $employeeQuery)->where('status', 2)->count();
        $background_verified = (clone $employeeQuery)->where('status', 4)->count();

        return response()->json([
            'message' => 'Success',
            'data' => [
                'total_offers' => $total_offers,
                'accepted_offers' => $accepted_offers,
                'pending_offers' => $pending_offers,
                'declined_offers' => $declined_offers,
                'background_verification_pending' => $background_verification_pending,
                'background_verified' => $background_verified
            ]
        ], 200);
    }
}
