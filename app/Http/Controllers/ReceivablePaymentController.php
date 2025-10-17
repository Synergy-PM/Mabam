<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use App\Models\ReceivablePayment;
use App\Models\Receivable;
use App\Models\Payable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceivablePaymentController extends Controller
{
    public function index()
    {
        $payments = ReceivablePayment::with('dealer')->latest()->get();
        return view('admin.receivable_payments.index', compact('payments'));
    }

    public function create()
    {
        $dealers = Dealer::all();
        return view('admin.receivable_payments.create', compact('dealers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payments' => 'required|array',
            'payments.*.dealer_id' => 'required|exists:dealers,id',
            'payments.*.transaction_date' => 'required|date',
            'payments.*.amount_received' => 'required|numeric|min:0',
            'payments.*.payment_mode' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->payments as $payment) {
                ReceivablePayment::create($payment);
            }

            DB::commit();
            return redirect()->route('receivable-payments.index')->with('success', 'Payments added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to save payments: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $payment = ReceivablePayment::findOrFail($id);
        $payment->delete();

        return redirect()->route('receivable-payments.index')->with('success', 'Payment deleted successfully!');
    }

     public function ledgerReportFilter()
    {
        $dealers = Dealer::all();
        return view('admin.receivable_payments.filter', compact('dealers'));
    }

    // public function ledgerReport(Request $request)
    // {
    //     $request->validate([
    //         'dealer_id' => 'nullable|exists:dealers,id',
    //         'start_date' => 'nullable|date',
    //         'end_date'   => 'nullable|date|after_or_equal:start_date',
    //     ]);

    //     $dealerId = $request->input('dealer_id');
    //     $startDate = $request->input('start_date');
    //     $endDate = $request->input('end_date');

    //     $receivablePaymentsQuery = ReceivablePayment::with('dealer')
    //         ->when($dealerId, function ($q) use ($dealerId) {
    //             return $q->where('dealer_id', $dealerId);
    //         })
    //         ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
    //             return $q->whereBetween('transaction_date', [$startDate, $endDate]);
    //         })
    //         ->when($startDate && !$endDate, function ($q) use ($startDate) {
    //             return $q->whereDate('transaction_date', $startDate);
    //         });

    //     $receivablePayments = $receivablePaymentsQuery->get();

    //     // dd($receivablePayments);

    //     $transactions = $receivablePayments->map(function ($payment) {
    //         return [
    //             'dealer' => $payment->dealer,
    //             'transaction_date' => $payment->transaction_date,
    //             'amount' => $payment->amount_received ?? 0,
    //             'payment_mode' => $payment->payment_mode ?? 'N/A',
    //             'transaction_type' => $payment->transaction_type ?? 'N/A',
    //             'freight' => 0, // if not used, can remove from view too
    //         ];
    //     })->sortBy('transaction_date')->values();

    //     $totalAmount = $transactions->sum('amount');
    //     $selectedDealer = $dealerId ? Dealer::find($dealerId) : null;

    //     return view('admin.receivable_payments.report', compact(
    //         'transactions',
    //         'selectedDealer',
    //         'startDate',
    //         'endDate',
    //         'totalAmount'
    //     ));
    // }

  public function ledgerReport(Request $request)
{
    $request->validate([
        'dealer_id' => 'nullable|exists:dealers,id',
        'start_date' => 'nullable|date',
        'end_date'   => 'nullable|date|after_or_equal:start_date',
    ]);

    $dealerId = $request->input('dealer_id');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // MAIN QUERY (FILTERED)
    $receivablePaymentsQuery = \App\Models\ReceivablePayment::with('dealer')
        ->when($dealerId, function ($q) use ($dealerId) {
            return $q->where('dealer_id', $dealerId);
        })
        ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
            return $q->whereBetween('transaction_date', [$startDate, $endDate]);
        })
        ->when($startDate && !$endDate, function ($q) use ($startDate) {
            return $q->whereDate('transaction_date', $startDate);
        });

    $receivablePayments = $receivablePaymentsQuery->get();

    // Individual Transactions List
    $transactions = $receivablePayments->map(function ($payment) {
        return [
            'dealer' => $payment->dealer,
            'transaction_date' => $payment->transaction_date,
            'amount' => $payment->amount_received ?? 0,
            'payment_mode' => $payment->payment_mode ?? 'N/A',
            'transaction_type' => $payment->transaction_type ?? 'N/A',
            'freight' => 0,
        ];
    })->sortBy('transaction_date')->values();

    $totalAmount = $transactions->sum('amount');
    $selectedDealer = $dealerId ? \App\Models\Dealer::find($dealerId) : null;

    // ✅ Dealer Summary (when no dealer selected)
    $dealerSummaries = [];
    if (!$dealerId) {
        $dealerSummaries = \DB::table('receivable_payments')
            ->join('dealers', 'receivable_payments.dealer_id', '=', 'dealers.id')
            ->leftJoin('receivables', 'receivables.dealer_id', '=', 'dealers.id')
            ->selectRaw('
                receivable_payments.dealer_id,
                dealers.dealer_name,
                COALESCE(SUM(receivables.tons), 0) as tons,
                SUM(CASE WHEN receivable_payments.transaction_type = "credit" THEN receivable_payments.amount_received ELSE 0 END) as total_credit,
                SUM(CASE WHEN receivable_payments.transaction_type = "debit" THEN receivable_payments.amount_received ELSE 0 END) as total_debit
            ')
            ->groupBy('receivable_payments.dealer_id', 'dealers.dealer_name')
            ->get()
            ->map(function ($item) {
                return [
                    'dealer_name'   => $item->dealer_name ?? 'N/A',
                    'tons'          => $item->tons ?? 0,
                    'total_credit'  => $item->total_credit ?? 0,
                    'total_debit'   => $item->total_debit ?? 0,
                ];
            });
    }

    return view('admin.receivable_payments.report', compact(
        'transactions',
        'selectedDealer',
        'startDate',
        'endDate',
        'totalAmount',
        'dealerSummaries'
    ));
}



}