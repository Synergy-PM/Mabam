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
        // $validated = $request->validate([
        //     'payments' => 'required|array|min:1',
        //     'payments.*.payable_id' => 'required|exists:payables,id',
        //     'payments.*.transaction_date' => 'required|date',
        //     'payments.*.amount_paid' => 'required|numeric|min:0',
        //     'payments.*.payment_mode' => 'required|in:cash,bank,cheque,online',
        //     'payments.*.transaction_type' => 'required|in:credit,debit', // Added validation
        //     'payments.*.proof_of_payment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        //     'payments.*.notes' => 'nullable|string',
        // ]);

        // Loop through each payment entry
        foreach ($request->payments as $index => $paymentData) {
            $payable = Payable::findOrFail($paymentData['payable_id']);

            $payment = new PayablePayment();
            $payment->payable_id = $payable->id;
            $payment->supplier_id = $payable->supplier_id;
            $payment->transaction_date = $paymentData['transaction_date'];
            $payment->amount = $paymentData['amount_paid'];
            $payment->payment_mode = $paymentData['payment_mode'];
            $payment->transaction_type = $paymentData['transaction_type']; // Save transaction_type
            $payment->notes = $paymentData['notes'] ?? null;

            // Handle file upload for proof of payment
            if (isset($paymentData['proof_of_payment']) && $request->hasFile("payments.{$index}.proof_of_payment")) {
                $payment->proof_of_payment = $request->file("payments.{$index}.proof_of_payment")->store('proofs', 'public');
            }

            $payment->save();
        }

        return redirect()->route('payable-payments.index')->with('success', 'Payments created successfully.');
    }

    public function destroy($id)
    {
        $payment = PayablePayment::findOrFail($id);
        $payment->delete();
        return redirect()->route('payable-payments.index')->with('success', 'Payment deleted successfully.');
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
        $query = \App\Models\PayablePayment::with('payable.supplier', 'supplier');

        if ($request->payable_id) {
            $query->where('payable_id', $request->payable_id);
        }

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->start_date) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        $payments = $query->orderBy('transaction_date')->get();
        $selectedPayable = \App\Models\Payable::with('supplier')->find($request->payable_id);
        $supplier = \App\Models\Supplier::where('id', $request->supplier_id)->first();

        return view('admin.payable_payments.ledger-report', compact(
            'payments',
            'selectedPayable',
            'supplier'
        ))->with([
            'startDate' => $request->start_date,
            'endDate' => $request->end_date
        ]);
    }

}
