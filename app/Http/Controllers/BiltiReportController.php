<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Receivable;
use App\Models\Dealer;
use App\Models\Supplier;
use App\Models\Payable;
use App\Models\Expense;
use Carbon\Carbon;
use App\Models\ReceivablePayment;

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

  public function showDailyReportFilter()
{
    $dealers = Dealer::all();
    $suppliers = Supplier::all();
    return view('admin.reports.daily-report-filter', compact('dealers', 'suppliers'));
}

// public function dailyReport(Request $request)
// {
//     $request->validate([
//         'supplier_id' => 'nullable|exists:suppliers,id',
//         'dealer_id' => 'nullable|exists:dealers,id',
//         'start_date' => 'nullable|date',
//         'end_date' => 'nullable|date|after_or_equal:start_date',
//     ]);

//     // ✅ Date logic fix (handle one-date or empty range)
//     $startDate = $request->input('start_date');
//     $endDate = $request->input('end_date');

//     if (!$startDate && !$endDate) {
//         $startDate = Carbon::today()->toDateString();
//         $endDate = Carbon::today()->toDateString();
//     } elseif ($startDate && !$endDate) {
//         $endDate = $startDate;
//     } elseif (!$startDate && $endDate) {
//         $startDate = $endDate;
//     }

//     $supplierId = $request->input('supplier_id');
//     $dealerId = $request->input('dealer_id');

//     $reports = collect();
//     $totalPayable = 0;
//     $totalReceivable = 0;
//     $totalExpense = 0;
//     $totalReceivablePayment = 0;
//     $totalAmount = 0;

//     // ✅ Payables
//     $payables = Payable::with('supplier')
//         ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
//         ->whereNotNull('transaction_date')
//         ->whereBetween('transaction_date', [$startDate, $endDate])
//         ->get()
//         ->map(function ($payable) {
//             return [
//                 'type' => 'payable',
//                 'name' => $payable->supplier->supplier_name ?? 'N/A',
//                 'amount' => ($payable->no_of_bags ?? 0) * ($payable->amount_per_bag ?? 0),
//                 'transaction_date' => $payable->transaction_date,
//                 'is_credit' => true,
//                 'payment_mode' => 'N/A',
//             ];
//         });

//     // ✅ Receivables
//     $receivables = Receivable::with(['dealer', 'payable'])
//         ->when($dealerId, fn($q) => $q->where('dealer_id', $dealerId))
//         ->where(function ($q) use ($startDate, $endDate) {
//             $q->whereHas('payable', function ($p) use ($startDate, $endDate) {
//                 $p->whereBetween('transaction_date', [$startDate, $endDate]);
//             })->orWhereBetween('created_at', [$startDate, $endDate]);
//         })
//         ->get()
//         ->map(function ($receivable) {
//             $transactionDate = $receivable->payable->transaction_date ?? $receivable->created_at;
//             return [
//                 'type' => 'receivable',
//                 'name' => $receivable->dealer->dealer_name ?? 'N/A',
//                 'amount' => (($receivable->bags ?? 0) * ($receivable->rate ?? 0)) - ($receivable->freight ?? 0),
//                 'transaction_date' => $transactionDate,
//                 'is_credit' => false,
//                 'payment_mode' => $receivable->payment_type ?? 'N/A',
//             ];
//         });

//     // ✅ Receivable Payments
//     $receivablePayments = ReceivablePayment::with('dealer')
//         ->when($dealerId, fn($q) => $q->where('dealer_id', $dealerId))
//         ->whereNotNull('transaction_date')
//         ->whereBetween('transaction_date', [$startDate, $endDate])
//         ->get()
//         ->map(function ($payment) {
//             return [
//                 'type' => 'receivable_payment',
//                 'name' => $payment->dealer->dealer_name ?? 'N/A',
//                 'amount' => $payment->amount_received ?? 0,
//                 'transaction_date' => $payment->transaction_date,
//                 'is_credit' => true,
//                 'payment_mode' => $payment->transaction_type ?? 'N/A',
//             ];
//         });

//     // ✅ Expenses
//     $expenses = Expense::whereNotNull('expense_date')
//         ->whereBetween('expense_date', [$startDate, $endDate])
//         ->get()
//         ->map(function ($expense) {
//             return [
//                 'type' => 'expense',
//                 'name' => $expense->expense_description ?? 'N/A',
//                 'amount' => $expense->amount ?? 0,
//                 'transaction_date' => $expense->expense_date,
//                 'is_credit' => true,
//                 'payment_mode' => 'N/A',
//             ];
//         });

//     // ✅ Merge and sort all data
//     $reports = $payables
//         ->concat($receivables)
//         ->concat($receivablePayments)
//         ->concat($expenses)
//         ->filter(fn($t) => !is_null($t['transaction_date']))
//         ->sortBy('transaction_date')
//         ->values();

//     // ✅ Totals
//     $totalPayable = $payables->sum('amount');
//     $totalReceivable = $receivables->sum('amount');
//     $totalReceivablePayment = $receivablePayments->sum('amount');
//     $totalExpense = $expenses->sum('amount');
//     $totalAmount = $totalReceivable + $totalReceivablePayment;
//     $netBalance = ($totalReceivable + $totalReceivablePayment) - ($totalPayable + $totalExpense);

//     $selectedSupplier = $supplierId ? Supplier::find($supplierId) : null;
//     $selectedDealer = $dealerId ? Dealer::find($dealerId) : null;

//     \Log::info('✅ Daily Transaction Report Generated', [
//         'supplier_id' => $supplierId,
//         'dealer_id' => $dealerId,
//         'start_date' => $startDate,
//         'end_date' => $endDate,
//         'payables' => $payables->count(),
//         'receivables' => $receivables->count(),
//         'receivable_payments' => $receivablePayments->count(),
//         'expenses' => $expenses->count(),
//         'reports' => $reports->count(),
//     ]);

//     if ($reports->isEmpty()) {
//         \Log::warning('⚠️ No transactions found for date range', [
//             'start_date' => $startDate,
//             'end_date' => $endDate,
//         ]);
//     }

//     return view('admin.reports.daily_report', compact(
//         'reports',
//         'totalPayable',
//         'totalReceivable',
//         'totalReceivablePayment',
//         'totalExpense',
//         'totalAmount',
//         'netBalance',
//         'selectedSupplier',
//         'selectedDealer',
//         'startDate',
//         'endDate'
//     ));
// }


public function dailyReport(Request $request)
{
    $request->validate([
        'supplier_id' => 'nullable|exists:suppliers,id',
        'dealer_id' => 'nullable|exists:dealers,id',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ]);
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    if (!$startDate && !$endDate) {
        $startDate = Carbon::today()->toDateString();
        $endDate = Carbon::today()->toDateString();
    } elseif ($startDate && !$endDate) {
        $endDate = $startDate;
    } elseif (!$startDate && $endDate) {
        $startDate = $endDate;
    }

    $supplierId = $request->input('supplier_id');
    $dealerId = $request->input('dealer_id');

    $reports = collect();
    $totalPayable = 0;
    $totalReceivable = 0;
    $totalExpense = 0;
    $totalReceivablePayment = 0;
    $totalAmount = 0;

    $payables = Payable::with('supplier')
        ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
        ->whereNotNull('transaction_date')
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->get()
        ->map(function ($payable) {
            return [
                'type' => 'payable',
                'name' => $payable->supplier->supplier_name ?? 'N/A',
                'amount' => ($payable->no_of_bags ?? 0) * ($payable->amount_per_bag ?? 0),
                'transaction_date' => $payable->transaction_date,
                'is_credit' => true,
                'payment_mode' => 'N/A',
            ];
        });

    $receivables = Receivable::with(['dealer', 'payable'])
        ->when($dealerId, fn($q) => $q->where('dealer_id', $dealerId))
        ->where(function ($q) use ($startDate, $endDate) {
            $q->whereHas('payable', function ($p) use ($startDate, $endDate) {
                $p->whereBetween('transaction_date', [$startDate, $endDate]);
            })->orWhereBetween('created_at', [$startDate, $endDate]);
        })
        ->get()
        ->map(function ($receivable) {
            $transactionDate = $receivable->payable->transaction_date ?? $receivable->created_at;
            return [
                'type' => 'receivable',
                'name' => $receivable->dealer->dealer_name ?? 'N/A',
                'amount' => (($receivable->bags ?? 0) * ($receivable->rate ?? 0)) - ($receivable->freight ?? 0),
                'transaction_date' => $transactionDate,
                'is_credit' => false,
                'payment_mode' => $receivable->payment_type ?? 'N/A',
            ];
        });

    $receivablePayments = ReceivablePayment::with('dealer')
        ->when($dealerId, fn($q) => $q->where('dealer_id', $dealerId))
        ->whereNotNull('transaction_date')
        ->whereBetween('transaction_date', [$startDate, $endDate])
        ->get()
        ->map(function ($payment) {
            return [
                'type' => 'receivable_payment',
                'name' => $payment->dealer->dealer_name ?? 'N/A',
                'amount' => $payment->amount_received ?? 0,
                'transaction_date' => $payment->transaction_date,
                'is_credit' => true,
                'payment_mode' => $payment->transaction_type ?? 'N/A',
            ];
        });

    $expenses = Expense::whereNotNull('expense_date')
        ->whereBetween('expense_date', [$startDate, $endDate])
        ->get()
        ->map(function ($expense) {
            return [
                'type' => 'expense',
                'name' => $expense->expense_description ?? 'N/A',
                'amount' => $expense->amount ?? 0,
                'transaction_date' => $expense->expense_date,
                'is_credit' => true,
                'payment_mode' => 'N/A',
            ];
        });

    $reports = $payables
        ->concat($receivables)
        ->concat($receivablePayments)
        ->concat($expenses)
        ->filter(fn($t) => !is_null($t['transaction_date']))
        ->sortBy('transaction_date')
        ->map(function ($item) {
            $type = 'expense';
            $supplierExists = \App\Models\Supplier::where('supplier_name', $item['name'])->exists();
            $dealerExists = \App\Models\Dealer::where('dealer_name', $item['name'])->exists();

            if ($supplierExists) {
                $type = 'Supplier';
            } elseif ($dealerExists) {
                $type = 'Dealer';
            }

            $item['detected_type'] = $type;
            return $item;
        })
        ->values();

    $totalPayable = $payables->sum('amount');
    $totalReceivable = $receivables->sum('amount');
    $totalReceivablePayment = $receivablePayments->sum('amount');
    $totalExpense = $expenses->sum('amount');
    $totalAmount = $totalReceivable + $totalReceivablePayment;
    $netBalance = ($totalReceivable + $totalReceivablePayment) - ($totalPayable + $totalExpense);

    $selectedSupplier = $supplierId ? Supplier::find($supplierId) : null;
    $selectedDealer = $dealerId ? Dealer::find($dealerId) : null;

    \Log::info(' Daily Transaction Report Generated', [
        'supplier_id' => $supplierId,
        'dealer_id' => $dealerId,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'payables' => $payables->count(),
        'receivables' => $receivables->count(),
        'receivable_payments' => $receivablePayments->count(),
        'expenses' => $expenses->count(),
        'reports' => $reports->count(),
    ]);

    if ($reports->isEmpty()) {
        \Log::warning('⚠️ No transactions found for date range', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    return view('admin.reports.daily_report', compact(
        'reports',
        'totalPayable',
        'totalReceivable',
        'totalReceivablePayment',
        'totalExpense',
        'totalAmount',
        'netBalance',
        'selectedSupplier',
        'selectedDealer',
        'startDate',
        'endDate'
    ));
}


}