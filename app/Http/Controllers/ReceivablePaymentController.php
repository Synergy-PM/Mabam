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
        $dealers = Dealer::all(); 
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
    
     public function ledgerReport(Request $request)
    {
        $request->validate([
            'dealer_id'  => 'nullable|exists:dealers,id',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $dealerId  = $request->input('dealer_id');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        $receivablePaymentsQuery = \App\Models\ReceivablePayment::with('dealer')
            ->leftJoin('receivables', function ($join) {
                $join->on('receivable_payments.dealer_id', '=', 'receivables.dealer_id')
                    ->on('receivable_payments.payable_id', '=', 'receivables.payable_id');
            })
            ->when($dealerId, fn($q) => $q->where('receivable_payments.dealer_id', $dealerId))
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('receivable_payments.transaction_date', [$startDate, $endDate]))
            ->when($startDate && !$endDate, fn($q) => $q->whereDate('receivable_payments.transaction_date', $startDate))
            ->select(
                'receivable_payments.*',
                'receivables.bags',
                'receivables.rate',
                \DB::raw('(receivables.bags / 20) as tons')
            )
            ->orderBy('receivable_payments.transaction_date', 'asc');

        $receivablePayments = $receivablePaymentsQuery->get();

        $regularTransactions = $receivablePayments->map(function ($payment) {
            return [
                'dealer'           => $payment->dealer,
                'transaction_date' => $payment->transaction_date,
                'amount'           => $payment->amount_received ?? 0,
                'payment_mode'     => $payment->payment_mode ?? 'N/A',
                'transaction_type' => $payment->transaction_type ?? 'N/A',
                'tons'             => $payment->tons ?? 0,
                'rate'             => $payment->rate ?? 0,
                'is_opening'       => false,
            ];
        });

        $openingTransactions = [];
        if ($dealerId) {
            $selectedDealer = \App\Models\Dealer::find($dealerId);
            if ($selectedDealer && $selectedDealer->opening_balance != 0) {
                $openingTransactions[] = [
                    'dealer'           => $selectedDealer,
                    'transaction_date' => now()->format('Y-m-d'),
                    'amount'           => $selectedDealer->opening_balance,
                    'payment_mode'     => 'Opening Balance',
                    'transaction_type' => $selectedDealer->transaction_type ?? 'credit',
                    'tons'             => 0,
                    'rate'             => 0,
                    'is_opening'       => true,
                ];
            }
        } else {
            $dealers = \App\Models\Dealer::where('opening_balance', '!=', 0)->get();
            foreach ($dealers as $dealer) {
                $openingTransactions[] = [
                    'dealer'           => $dealer,
                    'transaction_date' => now()->format('Y-m-d'),
                    'amount'           => $dealer->opening_balance,
                    'payment_mode'     => 'Opening',
                    'transaction_type' => $dealer->transaction_type ?? 'credit',
                    'tons'             => 0,
                    'rate'             => 0,
                    'is_opening'       => true,
                ];
            }
        }

        $transactions = collect(array_merge($openingTransactions, $regularTransactions->toArray()));

        $selectedDealer = $dealerId ? \App\Models\Dealer::find($dealerId) : null;

        $dealerSummaries = [];
        if (!$dealerId) {
            $dealerSummaries = \DB::table('receivable_payments')
                ->join('dealers', 'receivable_payments.dealer_id', '=', 'dealers.id')
                ->leftJoin('receivables', function ($join) {
                    $join->on('receivables.dealer_id', '=', 'dealers.id')
                        ->on('receivables.payable_id', '=', 'receivable_payments.payable_id');
                })
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
                    $openingBalance = $dealer->opening_balance ?? 0;
                    $openingType = strtolower($dealer->transaction_type ?? 'credit');
                    $totalCredit = ($openingType === 'credit' ? $openingBalance : 0) + ($item->transaction_credit ?? 0);
                    $totalDebit = ($openingType === 'debit' ? $openingBalance : 0) + ($item->transaction_debit ?? 0);
                    return [
                        'dealer_name'     => $item->dealer_name ?? 'N/A',
                        'tons'            => $item->tons ?? 0,
                        'total_credit'    => $totalCredit,
                        'total_debit'     => $totalDebit,
                        'closing_balance' => $totalCredit - $totalDebit,
                    ];
                });
        }

        return view('admin.receivable_payments.report', compact(
            'transactions',
            'selectedDealer',
            'dealerSummaries',
            'startDate',
            'endDate'
        ));
    }

     public function supplierSummary(Request $request)
    {
        $dealerId = $request->dealer_id;

        $query = \DB::table('receivable_payments')
            ->join('dealers', 'receivable_payments.dealer_id', '=', 'dealers.id')
            ->leftJoin('receivables', 'receivables.dealer_id', '=', 'receivable_payments.dealer_id')
            ->whereNull('receivable_payments.deleted_at')
            ->selectRaw('
                receivable_payments.dealer_id,
                dealers.dealer_name,
                COALESCE(SUM(DISTINCT receivables.bags) / 20, 0) as tons,
                SUM(CASE WHEN receivable_payments.transaction_type = "credit" THEN receivable_payments.amount_received ELSE 0 END) as transaction_credit,
                SUM(CASE WHEN receivable_payments.transaction_type = "debit" THEN receivable_payments.amount_received ELSE 0 END) as transaction_debit
            ')
            ->groupBy('receivable_payments.dealer_id', 'dealers.dealer_name');

        if ($dealerId) {
            $query->where('receivable_payments.dealer_id', $dealerId);
        }

        $dealerSummaries = $query->get()->map(function ($item) {
            $dealer = \App\Models\Dealer::find($item->dealer_id);

            $openingBalance = $dealer ? $dealer->opening_balance : 0;
            $openingType = $dealer ? strtolower($dealer->transaction_type ?? 'credit') : 'credit';

            $totalCredit = ($openingType === 'credit' ? $openingBalance : 0) + ($item->transaction_credit ?? 0);
            $totalDebit  = ($openingType === 'debit' ? $openingBalance : 0) + ($item->transaction_debit ?? 0);

            return [
                'dealer_name'     => $item->dealer_name ?? 'N/A',
                'tons'            => $item->tons ?? 0,
                'total_credit'    => $totalCredit,
                'total_debit'     => $totalDebit,
                'closing_balance' => $totalCredit - $totalDebit,
            ];
        });

        return view('admin.receivable_payments.summary', compact('dealerSummaries'));
    }

}