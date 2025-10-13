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

public function ledgerReport(Request $request)
{
    // Validate the request
    $request->validate([
        'dealer_id' => 'nullable|exists:dealers,id',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);

    $dealerId = $request->input('dealer_id');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Fetch Receivables
    $receivablesQuery = Receivable::with(['payable.supplier', 'dealer'])
        ->when($dealerId, function ($q) use ($dealerId) {
            return $q->where('dealer_id', $dealerId);
        })
        ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
            return $q->whereHas('payable', function ($subQuery) use ($startDate, $endDate) {
                $subQuery->whereBetween('transaction_date', [$startDate, $endDate]);
            });
        });

    // Fetch Receivable Payments
    $receivablePaymentsQuery = ReceivablePayment::with('dealer')
        ->when($dealerId, function ($q) use ($dealerId) {
            return $q->where('dealer_id', $dealerId);
        })
        ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
            return $q->whereBetween('transaction_date', [$startDate, $endDate]);
        });

    // Get the data
    $receivables = $receivablesQuery->get()->map(function ($receivable) {
        return [
            'type' => 'receivable',
            'dealer' => $receivable->dealer,
            'transaction_date' => $receivable->payable ? $receivable->payable->transaction_date : null,
            'bags' => $receivable->bags ?? 0,
            'rate' => $receivable->rate ?? 0,
            'freight' => $receivable->freight ?? 0,
            'payment_type' => $receivable->payment_type ?? 'N/A',
            'amount' => ($receivable->bags ?? 0) * ($receivable->rate ?? 0),
            'is_receivable' => true,
        ];
    });

    $receivablePayments = $receivablePaymentsQuery->get()->map(function ($payment) {
        return [
            'type' => 'payment',
            'dealer' => $payment->dealer,
            'transaction_date' => $payment->transaction_date,
            'bags' => 0,
            'rate' => 0,
            'freight' => 0,
            'payment_type' => $payment->transaction_type ?? 'N/A',
            'amount' => $payment->amount_received ?? 0,
            'is_receivable' => false,
        ];
    });

    // Merge and sort transactions
    $transactions = $receivables->concat($receivablePayments)
        ->sortBy(function ($transaction) {
            return $transaction['transaction_date'] ?? '9999-12-31'; // Handle null dates
        })
        ->values();

    // Calculate totals
    $totalCreditAmount = $receivablePayments->sum('amount');
    $totalDebitAmount = $receivables->sum('amount');
    $totalFreight = $receivables->sum('freight');

    $selectedDealer = $dealerId ? Dealer::find($dealerId) : null;

    \Log::info('Ledger Report Data', [
        'dealer_id' => $dealerId,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'receivables_count' => $receivables->count(),
        'payments_count' => $receivablePayments->count(),
        'transactions_count' => $transactions->count(),
    ]);

    return view('admin.receivable_payments.report', compact(
        'transactions',
        'selectedDealer',
        'startDate',
        'endDate',
        'totalCreditAmount',
        'totalDebitAmount',
        'totalFreight'
    ));
}
}
