<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Supplier;
use App\Models\Dealer;
use App\Models\Payable;
use App\Models\Receivable;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::latest()->paginate(10);
        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $dealers = Dealer::all();
        $payables = Payable::all();
        $biltis = Receivable::all();
        return view('admin.payments.create', compact('biltis', 'dealers', 'payables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bilti_no' => 'required|string',
            'dealer_id.*' => 'required|exists:dealers,id',
            'bags.*' => 'required|integer|min:0',
            'rate.*' => 'required|numeric|min:0',
            'freight.*' => 'nullable|numeric|min:0',
            'payment_type.*' => 'required|string',
            'proof_of_payment.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ]);

        $biltiNo = $request->bilti_no;
        $dealerIds = $request->dealer_id;
        $bags = $request->bags;
        $rates = $request->rate;
        $freights = $request->freight;
        $paymentTypes = $request->payment_type;
        $proofs = $request->file('proof_of_payment');

        foreach ($dealerIds as $index => $dealerId) {
            $payment = new Payment();
            $payment->bilti_no = $biltiNo;
            $payment->dealer_id = $dealerId;
            $payment->bags = $bags[$index];
            $payment->rate = $rates[$index];
            $payment->freight = $freights[$index] ?? 0;
            $payment->total = ($bags[$index] * $rates[$index]) - ($freights[$index] ?? 0);
            $payment->payment_type = $paymentTypes[$index];

            if(isset($proofs[$index])){
                $file = $proofs[$index];
                $fileName = time().'_'.$index.'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/payments'), $fileName);
                $payment->proof_of_payment = $fileName;
            }

            $payment->save();
        }

        return redirect()->route('payments.index')->with('success','Receivable(s) created successfully.');
    }

    public function edit($id)
    {
        $payment = Payment::findOrFail($id);
        $suppliers = Supplier::all();
        $dealers = Dealer::all();

        return view('admin.payments.edit', compact('payment', 'suppliers', 'dealers'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'type' => 'required|in:supplier,dealer',
            'reference_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'payment_mode' => 'nullable|in:cash,cheque,online',
        ]);

        $payment = Payment::findOrFail($id);
        $payment->update($request->all());

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully.');
    }

    public function trash()
    {
        $payments = Payment::onlyTrashed()->get();
        return view('admin.payments.trash', compact('payments'));
    }

    public function restore($id)
    {
        $payment = Payment::onlyTrashed()->findOrFail($id);
        $payment->restore();

        return redirect()->route('payments.trash')->with('success', 'Payment restored successfully.');
    }
}