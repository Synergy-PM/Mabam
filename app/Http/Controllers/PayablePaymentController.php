<?php
namespace App\Http\Controllers;

use App\Models\PayablePayment;
use App\Models\Payable;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PayablePaymentController extends Controller
{
    public function index()
    {
        $payments = PayablePayment::with('supplier')->paginate(10);
        $trashCount = PayablePayment::onlyTrashed()->count();
        return view('admin.payable_payments.index', compact('payments','trashCount'));
    }

    public function create()
    {
        $payables = Payable::with('supplier')->get();
        $supplier = Supplier::all();
        return view('admin.payable_payments.create', compact('payables', 'supplier'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payments' => 'required|array|min:1',
            'payments.*.supplier_id' => 'required|exists:suppliers,id',
            'payments.*.transaction_date' => 'required|date',
            'payments.*.amount_paid' => 'required|numeric|min:0',
            'payments.*.payment_mode' => 'required|in:cash,bank,cheque,online',
            'payments.*.transaction_type' => 'required|in:credit,debit',
        ]);

        try {
            foreach ($validated['payments'] as $paymentData) {
                $payable = Payable::where('supplier_id', $paymentData['supplier_id'])->latest()->first();

                $payment = new PayablePayment();
                $payment->payable_id = $payable?->id; 
                $payment->supplier_id = $paymentData['supplier_id'];
                $payment->transaction_date = $paymentData['transaction_date'];
                $payment->amount = $paymentData['amount_paid'];
                $payment->payment_mode = $paymentData['payment_mode'];
                $payment->transaction_type = $paymentData['transaction_type'];
                $payment->save();
            }

            return redirect()->route('payable-payments.index')
                ->with('success', 'Payments created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    //  public function show($id)
    // {
    //     $payment = PayablePayment::with('supplier', 'payable')->findOrFail($id);
    //     return view('admin.payable_payments.show', compact('payment'));
    // }

   public function edit($id)
{
    $payment = PayablePayment::with('payable.supplier')->findOrFail($id);
    $payables = Payable::with('supplier')->get();
    $suppliers = Supplier::all();
    
    return view('admin.payable_payments.edit', compact('payment', 'payables', 'suppliers'));
}

 public function update(Request $request, $id)
{
    $validated = $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'transaction_date' => 'required|date',
        'amount_paid' => 'required|numeric|min:0',
        'payment_mode' => 'required|in:cash,bank,cheque,online',
        'transaction_type' => 'required|in:credit,debit',
    ]);

    try {
        $payment = PayablePayment::findOrFail($id);
        
        // Check if payable exists for this supplier
        $payable = Payable::where('supplier_id', $validated['supplier_id'])->latest()->first();
        
        $payment->update([
            'payable_id' => $payable?->id,
            'supplier_id' => $validated['supplier_id'],
            'transaction_date' => $validated['transaction_date'],
            'amount' => $validated['amount_paid'],
            'payment_mode' => $validated['payment_mode'],
            'transaction_type' => $validated['transaction_type'],
        ]);

        return redirect()->route('payable-payments.index')
            ->with('success', 'Payment updated successfully.');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}


    public function destroy($id)
    {
        $payment = PayablePayment::findOrFail($id);
        $payment->delete();
        return redirect()->route('payable-payments.index')->with('success', 'Payment deleted successfully.');
    }

public function trash()
{
    $payments = PayablePayment::onlyTrashed()->with('supplier')->paginate(10);
    return view('admin.payable_payments.trash', compact('payments'));
}


public function restore($id)
{
    $payment = PayablePayment::onlyTrashed()->findOrFail($id);
    $payment->restore();

    return redirect()->route('payable-payments.index')
        ->with('success', 'Payment restored successfully.');
}

        public function getPayableDetails($id)
    {
        $payable = Payable::with('supplier')->findOrFail($id);

        return response()->json([
            'bilti_no' => $payable->bilti_no,
            'supplier_name' => $payable->supplier->supplier_name ?? '',
            'total_amount' => $payable->total_amount,
        ]);
    }
  
    public function ledgerFilter()
    {
        $suppliers = \App\Models\Supplier::all();
        return view('admin.payable_payments.ledger-filter', compact('suppliers'));
    }

//      public function ledgerReport(Request $request)
//    {
//     $request->validate([
//         'supplier_id' => 'nullable|exists:suppliers,id',
//         'payable_id'  => 'nullable|exists:payables,id',
//         'start_date'  => 'nullable|date',
//         'end_date'    => 'nullable|date|after_or_equal:start_date',
//     ]);

//     $query = \App\Models\PayablePayment::with('payable.supplier', 'supplier')
//         ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
//         ->when($request->payable_id, fn($q) => $q->where('payable_id', $request->payable_id))
//         ->when($request->start_date, fn($q) => $q->whereDate('transaction_date', '>=', $request->start_date))
//         ->when($request->end_date, fn($q) => $q->whereDate('transaction_date', '<=', $request->end_date))
//         ->orderBy('transaction_date', 'asc');

//     $payments = $query->get();

//     $selectedSupplier = \App\Models\Supplier::find($request->supplier_id);
//     $selectedPayable  = \App\Models\Payable::with('supplier')->find($request->payable_id);

//     // Initialize opening balance based on selected supplier
//     $openBalance = $selectedSupplier ? $selectedSupplier->opening_balance : 0;

//     $supplierSummaries = [];
//     if (!$selectedSupplier) {
//         $suppliers = \App\Models\Supplier::with(['payablePayments' => function ($q) {
//             $q->orderBy('transaction_date', 'asc');
//         }])->get();

//         foreach ($suppliers as $supplier) {
//             $payables = \App\Models\Payable::where('supplier_id', $supplier->id)->get();

//             $totalCredit = $supplier->payablePayments->where('transaction_type', 'credit')->sum('amount');
//             $totalDebit  = $supplier->payablePayments->where('transaction_type', 'debit')->sum('amount');

//             // Add opening_balance as debit
//             $totalDebit += $supplier->opening_balance;

//             $supplierSummaries[] = [
//                 'supplier_name'    => $supplier->supplier_name,
//                 'tons'             => $payables->sum('tons') ?? 0,
//                 'total_credit'     => $totalCredit,
//                 'total_debit'      => $totalDebit,
//                 'closing_balance'  => $totalCredit - $totalDebit,
//             ];
//         }
//     }

//     return view('admin.payable_payments.ledger-report', compact(
//         'payments',
//         'selectedSupplier',
//         'selectedPayable',
//         'supplierSummaries',
//         'openBalance'
//     ))->with([
//         'startDate' => $request->start_date,
//         'endDate'   => $request->end_date,
//     ]);
//    }
    //  public function ledgerReport(Request $request)
    // {
    //     $request->validate([
    //         'supplier_id' => 'nullable|exists:suppliers,id',
    //         'payable_id'  => 'nullable|exists:payables,id',
    //         'start_date'  => 'nullable|date',
    //         'end_date'    => 'nullable|date|after_or_equal:start_date',
    //     ]);

    //     $query = \App\Models\PayablePayment::with('payable.supplier', 'supplier')
    //         ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
    //         ->when($request->payable_id, fn($q) => $q->where('payable_id', $request->payable_id))
    //         ->when($request->start_date, fn($q) => $q->whereDate('transaction_date', '>=', $request->start_date))
    //         ->when($request->end_date, fn($q) => $q->whereDate('transaction_date', '<=', $request->end_date))
    //         ->orderBy('transaction_date', 'asc');

    //     $payments = $query->get();

    //     $selectedSupplier = \App\Models\Supplier::find($request->supplier_id);
    //     $selectedPayable  = \App\Models\Payable::with('supplier')->find($request->payable_id);

    //     $openBalance = 0;
    //     if ($selectedSupplier) {
    //         if ($selectedSupplier->transaction_type === 'credit') {
    //             $openBalance = $selectedSupplier->opening_balance ?? 0;  // Credit side
    //         } else {
    //             $openBalance = -($selectedSupplier->opening_balance ?? 0); // Debit side
    //         }
    //     }

    //     $supplierSummaries = [];
    //     if (!$selectedSupplier) {
    //         $suppliers = \App\Models\Supplier::with(['payablePayments' => function ($q) {
    //             $q->orderBy('transaction_date', 'asc');
    //         }])->get();

    //         foreach ($suppliers as $supplier) {
    //             $payables = \App\Models\Payable::where('supplier_id', $supplier->id)->get();

    //             $totalCredit = $supplier->payablePayments->where('transaction_type', 'credit')->sum('amount');
    //             $totalDebit  = $supplier->payablePayments->where('transaction_type', 'debit')->sum('amount');
    //             if ($supplier->transaction_type === 'credit') {
    //                 $totalCredit += $supplier->opening_balance ?? 0;
    //             } else {
    //                 $totalDebit += $supplier->opening_balance ?? 0;
    //             }

    //             $supplierSummaries[] = [
    //                 'supplier_name'    => $supplier->supplier_name,
    //                 'tons'             => $payables->sum('tons') ?? 0,
    //                 'total_credit'     => $totalCredit,
    //                 'total_debit'      => $totalDebit,
    //                 'closing_balance'  => $totalCredit - $totalDebit,
    //             ];
    //         }
    //     }

    //     return view('admin.payable_payments.ledger-report', compact(
    //         'payments',
    //         'selectedSupplier',
    //         'selectedPayable',
    //         'supplierSummaries',
    //         'openBalance'
    //     ))->with([
    //         'startDate' => $request->start_date,
    //         'endDate'   => $request->end_date,
    //     ]);
    // }
public function ledgerReport(Request $request)
{
    $request->validate([
        'supplier_id' => 'nullable|exists:suppliers,id',
        'start_date'  => 'nullable|date',
        'end_date'    => 'nullable|date|after_or_equal:start_date',
    ]);

    $query = \App\Models\PayablePayment::with('supplier')
        ->when($request->supplier_id, fn($q) => $q->where('supplier_id', $request->supplier_id))
        ->when($request->start_date, fn($q) => $q->whereDate('transaction_date', '>=', $request->start_date))
        ->when($request->end_date, fn($q) => $q->whereDate('transaction_date', '<=', $request->end_date))
        ->orderBy('transaction_date', 'asc');

    $payments = $query->get();

    // Fetch related payables for each payment based on supplier_id and transaction_date
    foreach ($payments as $payment) {
        $payable = \App\Models\Payable::where('supplier_id', $payment->supplier_id)
            ->where('transaction_date', $payment->transaction_date)
            ->first();
        $payment->payable = $payable; // Attach payable to payment
    }

    $selectedSupplier = \App\Models\Supplier::find($request->supplier_id);

    // Opening balance logic
    $openBalance = 0;
    if ($selectedSupplier) {
        if ($selectedSupplier->transaction_type === 'credit') {
            $openBalance = $selectedSupplier->opening_balance ?? 0;
        } else {
            $openBalance = -($selectedSupplier->opening_balance ?? 0);
        }
    }

    $supplierSummaries = [];
    if (!$selectedSupplier) {
        $suppliers = \App\Models\Supplier::with(['payablePayments' => function ($q) {
            $q->orderBy('transaction_date', 'asc');
        }])->get();

        foreach ($suppliers as $supplier) {
            $payables = \App\Models\Payable::where('supplier_id', $supplier->id)->get();

            $totalCredit = $supplier->payablePayments->where('transaction_type', 'credit')->sum('amount');
            $totalDebit = $supplier->payablePayments->where('transaction_type', 'debit')->sum('amount');
            if ($supplier->transaction_type === 'credit') {
                $totalCredit += $supplier->opening_balance ?? 0;
            } else {
                $totalDebit += $supplier->opening_balance ?? 0;
            }

            $supplierSummaries[] = [
                'supplier_name'    => $supplier->supplier_name,
                'tons'             => $payables->sum('tons') ?? 0,
                'total_credit'     => $totalCredit,
                'total_debit'      => $totalDebit,
                'closing_balance'  => $totalCredit - $totalDebit,
            ];
        }
    }

    return view('admin.payable_payments.ledger-report', compact(
        'payments',
        'selectedSupplier',
        'supplierSummaries',
        'openBalance'
    ))->with([
        'startDate' => $request->start_date,
        'endDate'   => $request->end_date,
    ]);
}

}
