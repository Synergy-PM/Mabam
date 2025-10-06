<?php

namespace App\Http\Controllers;

use App\Models\Payable;
use App\Models\Receivable;
use App\Models\Dealer;
use App\Models\Supplier;
use App\Models\PayablePayment;
use Illuminate\Http\Request;

class PayableController extends Controller
{
    public function index()
    {
        $payables = Payable::with('supplier')->latest()->paginate(10);
        $trashCount = Payable::onlyTrashed()->count();
        return view('admin.Payable.index', compact('payables', 'trashCount'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $dealers = Dealer::all();
        $biltis = Receivable::all();
        return view('admin.Payable.create', compact('suppliers', 'biltis', 'dealers'));
    }




  public function store(Request $request)
{
    $request->validate([
        'transaction_date'   => 'required|date',
        'supplier_id'        => 'required|exists:suppliers,id',
        'no_of_bags'         => 'required|numeric|min:0',
        'amount_per_bag'     => 'required|numeric|min:0',
        'bilti_no'           => 'required|string',

        // RECEIVABLE / VALIDATION
        'dealer_id.*'        => 'nullable|exists:dealers,id',
        'bags.*'             => 'nullable|integer|min:0',
        'rate.*'             => 'nullable|numeric|min:0',
        'freight.*'          => 'nullable|numeric|min:0',
        'payment_type.*'     => 'nullable|string',
        'proof_of_payment.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        'code.*'             => 'required|distinct|unique:receivables,code',
    ]);

    //  Create Payable
    $payable = Payable::create([
        'transaction_date' => $request->transaction_date,
        'supplier_id'      => $request->supplier_id,
        'no_of_bags'       => $request->no_of_bags,
        'amount_per_bag'   => $request->amount_per_bag,
        'total_amount'     => $request->no_of_bags * $request->amount_per_bag,
        'tons'             => $request->no_of_bags / 20,
        'bilti_no'         => $request->bilti_no,
    ]);
    //  Create Payable payment
    $payable = PayablePayment::create([
        'transaction_date' => $request->transaction_date,
        'supplier_id'      => $request->supplier_id,
        'amount'     => $request->no_of_bags * $request->amount_per_bag,
        'payment_mode'     => 'debit',
    ]);
    // Create Receivables
    $dealerIds    = $request->dealer_id ?? [];
    $bags         = $request->bags ?? [];
    $rates        = $request->rate ?? [];
    $freights     = $request->freight ?? [];
    $paymentTypes = $request->payment_type ?? [];
    $codes        = $request->code ?? [];
    $proofs       = $request->file('proof_of_payment') ?? [];

    foreach ($dealerIds as $index => $dealerId) {
        if (empty($dealerId)) continue;

        $payment = new Receivable();
        $payment->supplier_id   = $request->supplier_id;
        $payment->payable_id    = $payable->id;
        $payment->bilti_no      = $request->bilti_no;
        $payment->dealer_id     = $dealerId;
        $payment->bags          = $bags[$index] ?? 0;
        $payment->rate          = $rates[$index] ?? 0;
        $payment->freight       = $freights[$index] ?? 0;
        $payment->tons          = ($bags[$index] ?? 0) / 20;
        $payment->total         = (($bags[$index] ?? 0) * ($rates[$index] ?? 0)) - ($freights[$index] ?? 0);
        $payment->payment_type  = $paymentTypes[$index] ?? null;
        $payment->code          = $codes[$index];

        if (!empty($proofs) && isset($proofs[$index]) && $proofs[$index] instanceof \Illuminate\Http\UploadedFile) {
            $file = $proofs[$index];
            $fileName = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/payments'), $fileName);
            $payment->proof_of_payment = $fileName;
        }

        $payment->save();
    }

    return redirect()->route('payables.index')->with('success', 'Payable Stored Successfully.');
}



  public function edit($id)
{
    $payable = Payable::with('receivables')->findOrFail($id);
    $suppliers = Supplier::all();
    $dealers   = Dealer::all();

    return view('admin.Payable.edit', compact('payable', 'suppliers', 'dealers'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'transaction_date'   => 'required|date',
        'supplier_id'        => 'required|exists:suppliers,id',
        'no_of_bags'         => 'required|numeric|min:0',
        'amount_per_bag'     => 'required|numeric|min:0',
        'bilti_no'           => 'required|string',

        // RECEIVABLE / PAYMENT VALIDATION
        'dealer_id.*'        => 'nullable|exists:dealers,id',
        'bags.*'             => 'nullable|integer|min:0',
        'rate.*'             => 'nullable|numeric|min:0',
        'freight.*'          => 'nullable|numeric|min:0',
        'payment_type.*'     => 'nullable|string',
        'proof_of_payment.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
    ]);

    // ✅ Find Payable
    $payable = Payable::findOrFail($id);

    // ✅ Update Payable
    $payable->update([
        'transaction_date' => $request->transaction_date,
        'supplier_id'      => $request->supplier_id,
        'no_of_bags'       => $request->no_of_bags,
        'amount_per_bag'   => $request->amount_per_bag,
        'total_amount'     => $request->no_of_bags * $request->amount_per_bag,
        'tons'             => $request->no_of_bags / 20,
        'bilti_no'         => $request->bilti_no,
    ]);

    // ✅ Purane Receivables delete karke naya insert
    Receivable::where('payable_id', $payable->id)->delete();

    $dealerIds    = $request->dealer_id ?? [];
    $bags         = $request->bags ?? [];
    $rates        = $request->rate ?? [];
    $freights     = $request->freight ?? [];
    $paymentTypes = $request->payment_type ?? [];
    $proofs       = $request->file('proof_of_payment') ?? [];

    foreach ($dealerIds as $index => $dealerId) {
        if (empty($dealerId)) continue;

        $payment = new Receivable();
        $payment->supplier_id   = $request->supplier_id;
        $payment->payable_id    = $payable->id;
        $payment->bilti_no      = $request->bilti_no;
        $payment->dealer_id     = $dealerId;
        $payment->bags          = $bags[$index] ?? 0;
        $payment->rate          = $rates[$index] ?? 0;
        $payment->freight       = $freights[$index] ?? 0;
        $payment->tons          = ($bags[$index] ?? 0) / 20;
        $payment->total         = (($bags[$index] ?? 0) * ($rates[$index] ?? 0)) - ($freights[$index] ?? 0);
        $payment->payment_type  = $paymentTypes[$index] ?? null;

        // ✅ Proof of Payment Upload
        if (!empty($proofs) && isset($proofs[$index]) && $proofs[$index] instanceof \Illuminate\Http\UploadedFile) {
            $file = $proofs[$index];
            $fileName = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/payments'), $fileName);
            $payment->proof_of_payment = $fileName;
        }

        $payment->save();
    }

    return redirect()->route('payables.index')->with('success', 'Payable Updated Successfully.');
}


    public function destroy($id)
    {
        $payable = Payable::findOrFail($id);
        $payable->delete();
        return redirect()->route('payables.index')->with('success', 'Moved to trash.');
    }

    public function trash()
    {
        $payables = Payable::onlyTrashed()->with('supplier')->paginate(10);
        return view('admin.Payable.trash', compact('payables'));
    }

    public function restore($id)
    {
        Payable::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('payables.trash')->with('success', 'Restored.');
    }
    public function getLastPayable($id)
    {
        $payable = \App\Models\Payable::where('supplier_id', $id)
            ->latest()
            ->first();

        if (!$payable) {
            return response()->json([
                'rate' => 0,
                'total_amount' => 0
            ]);
        }

        return response()->json([
            'rate' => $payable->amount_per_bag,
            'total_amount' => $payable->no_of_bags * $payable->amount_per_bag
        ]);
    }
}
