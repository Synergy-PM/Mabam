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
    $payments = ReceivablePayment::with('dealer')
    ->latest()
        ->paginate(10);
    $trashCount = ReceivablePayment::onlyTrashed()->count(); // optional if you use soft deletes

    return view('admin.receivable_payments.index', compact('payments', 'trashCount'));
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
    public function edit($id)
    {
        $payment = ReceivablePayment::findOrFail($id);
        $dealers = Dealer::all(); // Fetch all dealers for the dropdown
        return view('admin.receivable_payments.edit', compact('payment', 'dealers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'dealer_id' => 'required|exists:dealers,id',
            'transaction_date' => 'required|date',
            'amount_received' => 'required|numeric|min:0',
            'payment_mode' => 'required|string|in:cash,bank,cheque,online',
            'transaction_type' => 'required|string|in:debit,credit',
        ]);

        DB::beginTransaction();
        try {
            $payment = ReceivablePayment::findOrFail($id);
            $payment->update($request->all());

            DB::commit();
            return redirect()->route('receivable-payments.index')->with('success', 'Payment updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update payment: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $payment = ReceivablePayment::findOrFail($id);
        $payment->delete();

        return redirect()->route('receivable-payments.index')->with('success', 'Payment deleted successfully!');
    }

     public function trash()
    {
        $payments = ReceivablePayment::onlyTrashed()
            ->with('dealer')
            ->latest()
            ->paginate(10);

        return view('admin.receivable_payments.trash', compact('payments'));
    }

    // ✅ Restore from trash
    public function restore($id)
    {
        $payment = ReceivablePayment::onlyTrashed()->findOrFail($id);
        $payment->restore();

        return redirect()->route('receivable-payments.index')
                         ->with('success', 'Payment restored successfully.');
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

    //     // MAIN QUERY (FILTERED)
    //     $receivablePaymentsQuery = \App\Models\ReceivablePayment::with('dealer')
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

    //     // Individual Transactions List
    //     $transactions = $receivablePayments->map(function ($payment) {
    //         return [
    //             'dealer' => $payment->dealer,
    //             'transaction_date' => $payment->transaction_date,
    //             'amount' => $payment->amount_received ?? 0,
    //             'payment_mode' => $payment->payment_mode ?? 'N/A',
    //             'transaction_type' => $payment->transaction_type ?? 'N/A',
    //             'freight' => 0,
    //         ];
    //     })->sortBy('transaction_date')->values();

    //     $totalAmount = $transactions->sum('amount');
    //     $selectedDealer = $dealerId ? \App\Models\Dealer::find($dealerId) : null;

    //     // ✅ Dealer Summary (when no dealer selected)
    //     $dealerSummaries = [];
    //     if (!$dealerId) {
    //         $dealerSummaries = \DB::table('receivable_payments')
    //             ->join('dealers', 'receivable_payments.dealer_id', '=', 'dealers.id')
    //             ->leftJoin('receivables', 'receivables.dealer_id', '=', 'dealers.id')
    //             ->selectRaw('
    //                 receivable_payments.dealer_id,
    //                 dealers.dealer_name,
    //                 COALESCE(SUM(receivables.tons), 0) as tons,
    //                 SUM(CASE WHEN receivable_payments.transaction_type = "credit" THEN receivable_payments.amount_received ELSE 0 END) as total_credit,
    //                 SUM(CASE WHEN receivable_payments.transaction_type = "debit" THEN receivable_payments.amount_received ELSE 0 END) as total_debit
    //             ')
    //             ->groupBy('receivable_payments.dealer_id', 'dealers.dealer_name')
    //             ->get()
    //             ->map(function ($item) {
    //                 return [
    //                     'dealer_name'   => $item->dealer_name ?? 'N/A',
    //                     'tons'          => $item->tons ?? 0,
    //                     'total_credit'  => $item->total_credit ?? 0,
    //                     'total_debit'   => $item->total_debit ?? 0,
    //                 ];
    //             });
    //     }

    //     return view('admin.receivable_payments.report', compact(
    //         'transactions',
    //         'selectedDealer',
    //         'startDate',
    //         'endDate',
    //         'totalAmount',
    //         'dealerSummaries'
    //     ));
    // }

//        public function ledgerReport(Request $request)
// {
//     $request->validate([
//         'dealer_id' => 'nullable|exists:dealers,id',
//         'start_date' => 'nullable|date',
//         'end_date'   => 'nullable|date|after_or_equal:start_date',
//     ]);

//     $dealerId = $request->input('dealer_id');
//     $startDate = $request->input('start_date');
//     $endDate = $request->input('end_date');

//     // MAIN QUERY (FILTERED)
//     $receivablePaymentsQuery = \App\Models\ReceivablePayment::with('dealer')
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

//     // Individual Transactions List
//     $transactions = $receivablePayments->map(function ($payment) {
//         return [
//             'dealer' => $payment->dealer,
//             'transaction_date' => $payment->transaction_date,
//             'amount' => $payment->amount_received ?? 0,
//             'payment_mode' => $payment->payment_mode ?? 'N/A',
//             'transaction_type' => $payment->transaction_type ?? 'N/A',
//             'freight' => 0,
//         ];
//     })->sortBy('transaction_date')->values();

//     $totalAmount = $transactions->sum('amount');
//     $selectedDealer = $dealerId ? \App\Models\Dealer::find($dealerId) : null;

//     // Set opening balance for the selected dealer
//     $openBalance = $selectedDealer ? $selectedDealer->opening_balance : 0;

//     // Dealer Summary (when no dealer selected)
//     $dealerSummaries = [];
//     if (!$dealerId) {
//         $dealerSummaries = \DB::table('receivable_payments')
//             ->join('dealers', 'receivable_payments.dealer_id', '=', 'dealers.id')
//             ->leftJoin('receivables', 'receivables.dealer_id', '=', 'dealers.id')
//             ->selectRaw('
//                 receivable_payments.dealer_id,
//                 dealers.dealer_name,
//                 COALESCE(SUM(receivables.tons), 0) as tons,
//                 SUM(CASE WHEN receivable_payments.transaction_type = "credit" THEN receivable_payments.amount_received ELSE 0 END) + dealers.opening_balance as total_credit,
//                 SUM(CASE WHEN receivable_payments.transaction_type = "debit" THEN receivable_payments.amount_received ELSE 0 END) as total_debit
//             ')
//             ->groupBy('receivable_payments.dealer_id', 'dealers.dealer_name', 'dealers.opening_balance')
//             ->get()
//             ->map(function ($item) {
//                 return [
//                     'dealer_name'    => $item->dealer_name ?? 'N/A',
//                     'tons'           => $item->tons ?? 0,
//                     'total_credit'   => $item->total_credit ?? 0,
//                     'total_debit'    => $item->total_debit ?? 0,
//                     'closing_balance' => ($item->total_credit ?? 0) - ($item->total_debit ?? 0),
//                 ];
//             });
//     }

//     return view('admin.receivable_payments.report', compact(
//         'transactions',
//         'selectedDealer',
//         'openBalance',
//         'dealerSummaries',
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

    // ✅ Opening Balance aur uska type set karna
    $openBalance = $selectedDealer ? $selectedDealer->opening_balance : 0;
    $openingType = $selectedDealer ? strtolower($selectedDealer->transaction_type ?? 'credit') : 'credit';

    // Dealer Summary (when no dealer selected)
    $dealerSummaries = [];
    if (!$dealerId) {
        $dealerSummaries = \DB::table('receivable_payments')
            ->join('dealers', 'receivable_payments.dealer_id', '=', 'dealers.id')
            ->leftJoin('receivables', 'receivables.dealer_id', '=', 'dealers.id')
            ->selectRaw('
                receivable_payments.dealer_id,
                dealers.dealer_name,
                COALESCE(SUM(receivables.tons), 0) as tons,
                SUM(CASE WHEN receivable_payments.transaction_type = "credit" THEN receivable_payments.amount_received ELSE 0 END) + dealers.opening_balance as total_credit,
                SUM(CASE WHEN receivable_payments.transaction_type = "debit" THEN receivable_payments.amount_received ELSE 0 END) as total_debit
            ')
            ->groupBy('receivable_payments.dealer_id', 'dealers.dealer_name', 'dealers.opening_balance')
            ->get()
            ->map(function ($item) {
                return [
                    'dealer_name'    => $item->dealer_name ?? 'N/A',
                    'tons'           => $item->tons ?? 0,
                    'total_credit'   => $item->total_credit ?? 0,
                    'total_debit'    => $item->total_debit ?? 0,
                    'closing_balance' => ($item->total_credit ?? 0) - ($item->total_debit ?? 0),
                ];
            });
    }

    return view('admin.receivable_payments.report', compact(
        'transactions',
        'selectedDealer',
        'openBalance',
        'openingType',
        'dealerSummaries',
        'startDate',
        'endDate',
        'totalAmount'
    ));
}


}