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

    //     $receivablePaymentsQuery = \App\Models\ReceivablePayment::with('dealer')
    //         ->leftJoin('receivables', 'receivable_payments.dealer_id', '=', 'receivables.dealer_id')
    //         ->when($dealerId, function ($q) use ($dealerId) {
    //             return $q->where('receivable_payments.dealer_id', $dealerId);
    //         })
    //         ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
    //             return $q->whereBetween('receivable_payments.transaction_date', [$startDate, $endDate]);
    //         })
    //         ->when($startDate && !$endDate, function ($q) use ($startDate) {
    //             return $q->whereDate('receivable_payments.transaction_date', $startDate);
    //         })
    //         ->select(
    //             'receivable_payments.*',
    //             'receivables.bags',
    //             'receivables.rate'
    //         );

    //     $receivablePayments = $receivablePaymentsQuery->get();

    //     $transactions = $receivablePayments->map(function ($payment) {
    //         return [
    //             'dealer' => $payment->dealer,
    //             'transaction_date' => $payment->transaction_date,
    //             'amount' => $payment->amount_received ?? 0,
    //             'payment_mode' => $payment->payment_mode ?? 'N/A',
    //             'transaction_type' => $payment->transaction_type ?? 'N/A',
    //             'tons' => $payment->bags ? $payment->bags / 20 : 0,
    //             'rate' => $payment->rate ?? 0,
    //             'freight' => 0,
    //         ];
    //     })->sortBy('transaction_date')->values();

    //     $totalAmount = $transactions->sum('amount');
    //     $selectedDealer = $dealerId ? \App\Models\Dealer::find($dealerId) : null;

    //     $openBalance = $selectedDealer ? $selectedDealer->opening_balance : 0;
    //     $openingType = $selectedDealer ? strtolower($selectedDealer->transaction_type ?? 'credit') : 'credit';

    //     $dealerSummaries = [];
    //     if (!$dealerId) {
    //         $dealerSummaries = \DB::table('receivable_payments')
    //             ->join('dealers', 'receivable_payments.dealer_id', '=', 'dealers.id')
    //             ->leftJoin('receivables', 'receivables.dealer_id', '=', 'dealers.id')
    //             ->selectRaw('
    //                 receivable_payments.dealer_id,
    //                 dealers.dealer_name,
    //                 COALESCE(SUM(receivables.bags) / 20, 0) as tons,
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
    //         'openingType',
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

    $receivablePaymentsQuery = \App\Models\ReceivablePayment::with('dealer')
        ->leftJoin('receivables', 'receivable_payments.dealer_id', '=', 'receivables.dealer_id')
        ->when($dealerId, function ($q) use ($dealerId) {
            return $q->where('receivable_payments.dealer_id', $dealerId);
        })
        ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
            return $q->whereBetween('receivable_payments.transaction_date', [$startDate, $endDate]);
        })
        ->when($startDate && !$endDate, function ($q) use ($startDate) {
            return $q->whereDate('receivable_payments.transaction_date', $startDate);
        })
        ->select(
            'receivable_payments.*',
            'receivables.bags',
            'receivables.rate'
        );

    $receivablePayments = $receivablePaymentsQuery->get();

    $openingTransactions = [];
    $regularTransactions = $receivablePayments->map(function ($payment) {
        return [
            'dealer' => $payment->dealer,
            'transaction_date' => $payment->transaction_date,
            'amount' => $payment->amount_received ?? 0,
            'payment_mode' => $payment->payment_mode ?? 'N/A',
            'transaction_type' => $payment->transaction_type ?? 'N/A',
            'tons' => $payment->bags ? $payment->bags / 20 : 0,
            'rate' => $payment->rate ?? 0,
            'freight' => 0,
            'is_opening' => false,
        ];
    })->sortBy('transaction_date')->values();

    if ($dealerId) {
        $selectedDealer = \App\Models\Dealer::find($dealerId);
        if ($selectedDealer && $selectedDealer->opening_balance != 0) {
            $openingTransactions[] = [
                'dealer' => $selectedDealer,
                'transaction_date' => now()->format('Y-m-d'),
                'amount' => $selectedDealer->opening_balance,
                'payment_mode' => 'Opening Balance',
                'transaction_type' => $selectedDealer->transaction_type ?? 'credit',
                'tons' => 0,
                'rate' => 0,
                'freight' => 0,
                'is_opening' => true,
            ];
        }
    }

    if (!$dealerId) {
        $dealers = \App\Models\Dealer::where('opening_balance', '!=', 0)->get();
        foreach ($dealers as $dealer) {
            $openingTransactions[] = [
                'dealer' => $dealer,
                'transaction_date' => now()->format('Y-m-d'),
                'amount' => $dealer->opening_balance,
                'payment_mode' => 'Opening',
                'transaction_type' => $dealer->transaction_type ?? 'credit',
                'tons' => 0,
                'rate' => 0,
                'freight' => 0,
                'is_opening' => true,
            ];
        }
    }

    $transactions = collect(array_merge($openingTransactions, $regularTransactions->all()));

    $totalAmount = $transactions->where('is_opening', false)->sum('amount');
    $selectedDealer = $dealerId ? \App\Models\Dealer::find($dealerId) : null;

    $openBalance = $selectedDealer ? $selectedDealer->opening_balance : 0;
    $openingType = $selectedDealer ? strtolower($selectedDealer->transaction_type ?? 'credit') : 'credit';

    $dealerSummaries = [];
    if (!$dealerId) {
        $dealerSummaries = \DB::table('receivable_payments')
            ->join('dealers', 'receivable_payments.dealer_id', '=', 'dealers.id')
            ->leftJoin('receivables', 'receivables.dealer_id', '=', 'dealers.id')
            ->selectRaw('
                receivable_payments.dealer_id,
                dealers.dealer_name,
                COALESCE(SUM(receivables.bags) / 20, 0) as tons,
                SUM(CASE WHEN receivable_payments.transaction_type = "credit" THEN receivable_payments.amount_received ELSE 0 END) as transaction_credit,
                SUM(CASE WHEN receivable_payments.transaction_type = "debit" THEN receivable_payments.amount_received ELSE 0 END) as transaction_debit
            ')
            ->groupBy('receivable_payments.dealer_id', 'dealers.dealer_name')
            ->get()
            ->map(function ($item) {
                $dealer = \App\Models\Dealer::find($item->dealer_id);
                $openingBalance = $dealer ? $dealer->opening_balance : 0;
                $openingType = $dealer ? strtolower($dealer->transaction_type ?? 'credit') : 'credit';
                $totalCredit = ($openingType === 'credit' ? $openingBalance : 0) + ($item->transaction_credit ?? 0);
                $totalDebit = ($openingType === 'debit' ? $openingBalance : 0) + ($item->transaction_debit ?? 0);
                return [
                    'dealer_name'    => $item->dealer_name ?? 'N/A',
                    'tons'           => $item->tons ?? 0,
                    'total_credit'   => $totalCredit,
                    'total_debit'    => $totalDebit,
                    'closing_balance' => $totalCredit - $totalDebit,
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

public function supplierSummary(Request $request)
{
    $dealerId = $request->dealer_id;

    $dealerSummaries = [];

    if (!$dealerId) {
        $dealerSummaries = \DB::table('receivable_payments')
            ->join('dealers', 'receivable_payments.dealer_id', '=', 'dealers.id')
            ->leftJoin('receivables', 'receivables.dealer_id', '=', 'dealers.id')
            ->selectRaw('
                receivable_payments.dealer_id,
                dealers.dealer_name,
                COALESCE(SUM(receivables.bags) / 20, 0) as tons,
                SUM(CASE WHEN receivable_payments.transaction_type = "credit" THEN receivable_payments.amount_received ELSE 0 END) as transaction_credit,
                SUM(CASE WHEN receivable_payments.transaction_type = "debit" THEN receivable_payments.amount_received ELSE 0 END) as transaction_debit
            ')
            ->groupBy('receivable_payments.dealer_id', 'dealers.dealer_name')
            ->get()
            ->map(function ($item) {
                $dealer = \App\Models\Dealer::find($item->dealer_id);
                $openingBalance = $dealer ? $dealer->opening_balance : 0;
                $openingType = $dealer ? strtolower($dealer->transaction_type ?? 'credit') : 'credit';

                $totalCredit = ($openingType === 'credit' ? $openingBalance : 0) + ($item->transaction_credit ?? 0);
                $totalDebit  = ($openingType === 'debit' ? $openingBalance : 0) + ($item->transaction_debit ?? 0);

                return [
                    'dealer_name'      => $item->dealer_name ?? 'N/A',
                    'tons'             => $item->tons ?? 0,
                    'total_credit'     => $totalCredit,
                    'total_debit'      => $totalDebit,
                    'closing_balance'  => $totalCredit - $totalDebit,
                ];
            });
    }

    return view('admin.receivable_payments.summary', compact('dealerSummaries'));
}


}