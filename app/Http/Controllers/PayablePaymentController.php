<?php
namespace App\Http\Controllers;

use App\Models\PayablePayment;
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
        $suppliers = Supplier::all();
        return view('admin.payable_payments.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:0',
            'payment_mode' => 'nullable|string',
            'proof_of_payment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('proof_of_payment')) {
            $validated['proof_of_payment'] = $request->file('proof_of_payment')->store('proofs', 'public');
        }

        PayablePayment::create($validated);

        return redirect()->route('payable-payments.index')->with('success', 'Payable Payment created successfully.');
    }

    public function edit($id)
    {
        $payment = PayablePayment::findOrFail($id);
        $suppliers = Supplier::all();
        return view('admin.payable_payments.edit', compact('payment', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $payment = PayablePayment::findOrFail($id);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:0',
            'payment_mode' => 'nullable|string',
            'proof_of_payment' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('proof_of_payment')) {
            $validated['proof_of_payment'] = $request->file('proof_of_payment')->store('proofs', 'public');
        }

        $payment->update($validated);

        return redirect()->route('payable-payments.index')->with('success', 'Payable Payment updated successfully.');
    }

    public function destroy($id)
    {
        $payment = PayablePayment::findOrFail($id);
        $payment->delete();

        return redirect()->route('payable-payments.index')->with('success', 'Payable Payment deleted successfully.');
    }

    public function trash()
    {
        $payments = PayablePayment::onlyTrashed()->get();
        return view('admin.payable_payments.trash', compact('payments'));
    }

    public function restore($id)
    {
        $payment = PayablePayment::onlyTrashed()->findOrFail($id);
        $payment->restore();

        return redirect()->route('payable-payments.index')->with('success', 'Payable Payment restored successfully.');
    }
}

