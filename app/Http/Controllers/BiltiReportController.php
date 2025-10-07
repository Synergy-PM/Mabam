<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receivable;
use App\Models\Dealer;
use App\Models\Payable;

class BiltiReportController extends Controller
{
    public function showFilter()
    {
        $dealers = Dealer::all();
        $biltiList = Payable::select('bilti_no')->distinct()->get();

        return view('admin.reports.bilti-report-filter', compact('dealers', 'biltiList'));
    }

    public function index(Request $request)
    {
        $query = Receivable::with(['payable.supplier', 'dealer']);

        if ($request->filled('bilti_no')) {
            $query->whereHas('payable', function ($q) use ($request) {
                $q->where('bilti_no', 'like', '%' . $request->bilti_no . '%');
            });
        }

        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }

        if ($request->filled('start_date')) {
            $query->whereHas('payable', function ($q) use ($request) {
                $q->whereDate('transaction_date', '>=', $request->start_date);
            });
        }

        if ($request->filled('end_date')) {
            $query->whereHas('payable', function ($q) use ($request) {
                $q->whereDate('transaction_date', '<=', $request->end_date);
            });
        }

        $receivables = $query->get();

        $selectedDealer = Dealer::find($request->dealer_id);
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $dealers = Dealer::all();

        return view('admin.reports.bilti-report-results', compact(
            'receivables',
            'selectedDealer',
            'startDate',
            'endDate',
            'dealers'
        ));
    }

    public function getDealerByBilti($biltiNo)
    {
        try {
            $receivables = Receivable::whereHas('payable', function ($query) use ($biltiNo) {
                $query->where('bilti_no', $biltiNo);
            })->with('dealer')->get();

            if ($receivables->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No dealers found for this bilti',
                    'dealers' => [],
                ]);
            }

            $dealers = $receivables->pluck('dealer')->filter()->unique('id')->map(function ($dealer) {
                return [
                    'id' => $dealer->id,
                    'dealer_name' => $dealer->dealer_name,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'dealers' => $dealers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching dealers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}