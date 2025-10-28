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

        foreach ($payments as $payment) {
            $payable = \App\Models\Payable::where('supplier_id', $payment->supplier_id)
                ->where('transaction_date', $payment->transaction_date)
                ->first();
            $payment->payable = $payable; 
        }

        $selectedSupplier = \App\Models\Supplier::find($request->supplier_id);

        $transactions = [];
        $regularPayments = $payments->map(function ($payment) {
            return [
                'supplier' => $payment->supplier,
                'transaction_date' => $payment->transaction_date,
                'amount' => $payment->amount,
                'payment_mode' => $payment->payment_mode ?? 'N/A',
                'transaction_type' => $payment->transaction_type ?? 'N/A',
                'tons' => $payment->payable ? $payment->payable->tons ?? 0 : 0,
                'rate' => $payment->payable ? $payment->payable->amount_per_bag ?? 0 : 0,
                'is_opening' => false,
            ];
        })->sortBy('transaction_date')->values();

        if ($selectedSupplier) {
            if ($selectedSupplier->opening_balance != 0) {
                $transactions[] = [
                    'supplier' => $selectedSupplier,
                    'transaction_date' => now()->format('Y-m-d'),
                    'amount' => $selectedSupplier->opening_balance,
                    'payment_mode' => 'Opening Balance',
                    'transaction_type' => $selectedSupplier->transaction_type ?? 'credit',
                    'tons' => 0,
                    'rate' => 0,
                    'is_opening' => true,
                ];
            }
        }

        if (!$selectedSupplier) {
            $suppliers = \App\Models\Supplier::where('opening_balance', '!=', 0)->get();
            foreach ($suppliers as $supplier) {
                $transactions[] = [
                    'supplier' => $supplier,
                    'transaction_date' => now()->format('Y-m-d'),
                    'amount' => $supplier->opening_balance,
                    'payment_mode' => 'Opening Balance',
                    'transaction_type' => $supplier->transaction_type ?? 'credit',
                    'tons' => 0,
                    'rate' => 0,
                    'is_opening' => true,
                ];
            }
        }

        $transactions = collect(array_merge($transactions, $regularPayments->all()));

        $openBalance = 0;
        if ($selectedSupplier) {
            $openBalance = $selectedSupplier->transaction_type === 'credit' ? ($selectedSupplier->opening_balance ?? 0) : -($selectedSupplier->opening_balance ?? 0);
        }

        $supplierSummaries = [];
        if (!$selectedSupplier) {
            $suppliers = \App\Models\Supplier::with(['payablePayments' => function ($q) use ($request) {
                $q->when($request->start_date, fn($q) => $q->whereDate('transaction_date', '>=', $request->start_date))
                ->when($request->end_date, fn($q) => $q->whereDate('transaction_date', '<=', $request->end_date))
                ->orderBy('transaction_date', 'asc');
            }])->get();

            foreach ($suppliers as $supplier) {
                $payables = \App\Models\Payable::where('supplier_id', $supplier->id)
                    ->when($request->start_date, fn($q) => $q->whereDate('transaction_date', '>=', $request->start_date))
                    ->when($request->end_date, fn($q) => $q->whereDate('transaction_date', '<=', $request->end_date))
                    ->get();

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
            'transactions',
            'selectedSupplier',
            'supplierSummaries',
            'openBalance'
        ))->with([
            'startDate' => $request->start_date,
            'endDate'   => $request->end_date,
        ]);
    }

    public function supplierSummary(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $selectedSupplier = $request->supplier_id;

        $supplierSummaries = [];

        $suppliers = \App\Models\Supplier::select('id', 'supplier_name', 'opening_balance', 'transaction_type')->get();

        foreach ($suppliers as $supplier) {
            $payables = \App\Models\Payable::where('supplier_id', $supplier->id)->get();
            $tons = $payables->sum('tons') ?? 0;

            $payments = \App\Models\PayablePayment::where('supplier_id', $supplier->id)
                ->when($startDate, fn($q) => $q->whereDate('transaction_date', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('transaction_date', '<=', $endDate))
                ->orderBy('transaction_date', 'asc')
                ->get();

            $totalCredit = $payments->where('transaction_type', 'credit')->sum('amount');
            $totalDebit  = $payments->where('transaction_type', 'debit')->sum('amount');

            if ($supplier->transaction_type === 'credit') {
                $totalCredit += $supplier->opening_balance ?? 0;
            } else {
                $totalDebit += $supplier->opening_balance ?? 0;
            }

            $closingBalance = $totalCredit - $totalDebit;

            $supplierSummaries[] = [
                'supplier_name'   => $supplier->supplier_name,
                'tons'            => $tons,
                'total_credit'    => $totalCredit,
                'total_debit'     => $totalDebit,
                'closing_balance' => $closingBalance,
            ];
        }

        return view('admin.payable_payments.summary', compact('supplierSummaries'))
            ->with(['startDate' => $startDate, 'endDate' => $endDate]);
    }
}
