<?php

namespace App\Http\Controllers\Api;

use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Offer;
use Carbon\Carbon;
use Dflydev\DotAccessData\Data;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from_date' => 'sometimes|date',
            'to_date' => 'sometimes|date'    
        ]);

        $offerQuery = Offer::query();
        $employeeQuery = Employee::query();

        if ($request->filled(['from_date', 'to_date'])) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate   = Carbon::parse($request->to_date)->endOfDay();

            $offerQuery->whereBetween('created_at', [$fromDate, $toDate]);
            $employeeQuery->whereBetween('created_at', [$fromDate, $toDate]);
        }

        $candidatesWithPendingFormCompletion = (clone $employeeQuery)->select('id', 'first_name', 'last_name', 'email', 'mobile')->where('status', 2)->get();

        return response()->json([
            'message' => 'Success',
            'data' => [
                'cards' => $this->getDataForCards($offerQuery, $employeeQuery),
                'candidates_with_pending_form_completion' => $candidatesWithPendingFormCompletion,
                'graph' => $this->getDataForGraph($offerQuery, $employeeQuery)
            ],
        ], 200);
    }

    private function getDataForCards(Builder $offerQuery, Builder $employeeQuery): array {

        $totalOffers = $offerQuery->count();
        $acceptedOffers = (clone $offerQuery)->where('is_accepted', 1)->count();
        $declinedOffers = (clone $offerQuery)->where('is_declined', 1)->count();
        $pendingOffers = (clone $offerQuery)->where('status', OfferStatus::PENDING)->count();

        $backgroundVerificationPending = (clone $employeeQuery)->where('status', OfferStatus::ACCEPTED)->count();
        $backgroundVerified = (clone $employeeQuery)->where('status', OfferStatus::REGISTERED_EMPLOYEE)->count();

        return [
            'total_offers' => $totalOffers,
            'accepted_offers' => $acceptedOffers,
            'pending_offers' => $pendingOffers,
            'declined_offers' => $declinedOffers,
            'background_verification_pending' => $backgroundVerificationPending,
            'background_verified' => $backgroundVerified
        ];
    }

    private function getDataForGraph(Builder $offerQuery, Builder $employeeQuery): Collection {

        $start = Carbon::now()->startOfYear();
        $end = Carbon::now()->endOfYear();

        $offerCounts = Offer::selectRaw('
                MONTH(created_at) as month,
                COUNT(*) as offers,
                SUM(CASE WHEN is_accepted = 1 THEN 1 ELSE 0 END) as accepted
            ')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $months = collect();
        $current = $start->copy();

        while ($current <= $end) {

            $monthKey = (int) $current->format('m');
            $monthName = $current->format('M');

            $months->push([
                'month' => $monthName,
                'offers' => isset($offerCounts[$monthKey]) ? (int) $offerCounts[$monthKey]->offers : 0,
                'accepted' => isset($offerCounts[$monthKey]) ? (int) $offerCounts[$monthKey]->accepted : 0,
            ]);

            $current->addMonth();
        }

        return $months;
    }
}
