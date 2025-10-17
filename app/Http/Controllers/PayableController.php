<?php

namespace App\Http\Controllers;

use App\Models\Payable;
use App\Models\Receivable;
use App\Models\Dealer;
use App\Models\Supplier;
use App\Models\PayablePayment;
use App\Models\ReceivablePayment;
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
            'dealer_id.*'        => 'nullable|exists:dealers,id',
            'bags.*'             => 'nullable|integer|min:0',
            'rate.*'             => 'nullable|numeric|min:0',
            'freight.*'          => 'nullable|numeric|min:0',
            'payment_type.*'     => 'nullable|string',
            'proof_of_payment.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'code.*'             => 'required|distinct|unique:receivables,code',
        ]);

        $tons = $request->no_of_bags / 20;

        $payable = Payable::create([
            'transaction_date' => $request->transaction_date,
            'supplier_id'      => $request->supplier_id,
            'no_of_bags'       => $request->no_of_bags,
            'amount_per_bag'   => $request->amount_per_bag,
            'total_amount'     => $tons * $request->amount_per_bag,
            'tons'             => $tons,
            'bilti_no'         => $request->bilti_no,
        ]);

        PayablePayment::create([
            'transaction_date' => $request->transaction_date,
            'supplier_id'      => $request->supplier_id,
            'amount'           => $tons * $request->amount_per_bag,
            'payment_mode'     => 'debit',
            'transaction_type' => 'debit',
        ]);

        $dealerIds    = $request->dealer_id ?? [];
        $bags         = $request->bags ?? [];
        $rates        = $request->rate ?? [];
        $freights     = $request->freight ?? [];
        $paymentTypes = $request->payment_type ?? [];
        $codes        = $request->code ?? [];
        $proofs       = $request->file('proof_of_payment') ?? [];

        foreach ($dealerIds as $index => $dealerId) {
            if (empty($dealerId)) continue;

            $bagsCount  = (float) ($bags[$index] ?? 0);
            $rateValue  = (float) ($rates[$index] ?? 0);
            $freightAmt = (float) ($freights[$index] ?? 0);

            $receivable = new Receivable();
            $receivable->supplier_id   = $request->supplier_id;
            $receivable->payable_id    = $payable->id;
            $receivable->bilti_no      = $request->bilti_no;
            $receivable->dealer_id     = $dealerId;
            $receivable->bags          = $bagsCount;
            $receivable->rate          = $rateValue;
            $receivable->freight       = $freightAmt;
            $receivable->tons          = $bagsCount / 20;

            $receivable->total         = ($bagsCount * ($rateValue - $freightAmt));

            $receivable->payment_type  = $paymentTypes[$index] ?? null;
            $receivable->code          = $codes[$index];

            if (!empty($proofs) && isset($proofs[$index]) && $proofs[$index] instanceof \Illuminate\Http\UploadedFile) {
                $file = $proofs[$index];
                $fileName = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/payments'), $fileName);
                $receivable->proof_of_payment = $fileName;
            }

            $receivable->save();

            ReceivablePayment::create([
                'payable_id'       => $payable->id,
                'dealer_id'        => $dealerId,
                'transaction_date' => $request->transaction_date,
                'amount_received'  => $receivable->total,
                'payment_mode'     => $receivable->payment_type,
                'transaction_type' => 'credit',
                'proof_of_payment' => $receivable->proof_of_payment ?? null,
            ]);
        }

        return redirect()->route('payables.index')->with('success', 'Payable, Receivable & ReceivablePayment stored successfully.');
    }



    public function edit($id)
    {
        $payable = Payable::with(['supplier', 'receivables.dealer'])->findOrFail($id);
        $suppliers = Supplier::all();
        $dealers = Dealer::all();
        $receivables = $payable->receivables()->with('dealer')->get();
        
        return view('admin.Payable.edit', compact('payable', 'suppliers', 'dealers', 'receivables'));
    }

   public function update(Request $request, $id)
{
    $payable = Payable::findOrFail($id);
    
    $request->validate([
        'transaction_date'   => 'required|date',
        'supplier_id'        => 'required|exists:suppliers,id',
        'no_of_bags'         => 'required|numeric|min:0',
        'amount_per_bag'     => 'required|numeric|min:0',
        'bilti_no'           => 'required|string',
        'dealer_id.*'        => 'nullable|exists:dealers,id',
        'bags.*'             => 'nullable|integer|min:0',
        'rate.*'             => 'nullable|numeric|min:0',
        'freight.*'          => 'nullable|numeric|min:0',
        'payment_type.*'     => 'nullable|string',
        'proof_of_payment.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ]);

    $tons = $request->no_of_bags / 20;

    $payable->update([
        'transaction_date' => $request->transaction_date,
        'supplier_id'      => $request->supplier_id,
        'no_of_bags'       => $request->no_of_bags,
        'amount_per_bag'   => $request->amount_per_bag,
        'total_amount'     => $tons * $request->amount_per_bag,
        'tons'             => $tons,
        'bilti_no'         => $request->bilti_no,
    ]);

    Receivable::where('payable_id', $payable->id)->delete();

    $dealerIds = $request->input('dealer_id', []);
    $bags = $request->input('bags', []);
    $rates = $request->input('rate', []);
    $freights = $request->input('freight', []);
    $paymentTypes = $request->input('payment_type', []);
    $proofFiles = $request->file('proof_of_payment', []);

    foreach ($dealerIds as $index => $dealerId) {
        if (empty($dealerId)) continue;

        $bagsCount = (float) ($bags[$index] ?? 0);
        $rateValue = (float) ($rates[$index] ?? 0);
        $freightAmt = (float) ($freights[$index] ?? 0);

        do {
            $code = mt_rand(100000, 999999); 
        } while (Receivable::where('code', $code)->exists());

        $receivable = new Receivable();
        $receivable->supplier_id = $request->supplier_id;
        $receivable->payable_id = $payable->id;
        $receivable->bilti_no = $request->bilti_no;
        $receivable->dealer_id = $dealerId;
        $receivable->bags = $bagsCount;
        $receivable->rate = $rateValue;
        $receivable->freight = $freightAmt;
        $receivable->tons = $bagsCount / 20;
        $receivable->total = $bagsCount * ($rateValue - $freightAmt);
        $receivable->payment_type = $paymentTypes[$index] ?? null;
        $receivable->code = $code;

        if (isset($proofFiles[$index]) && $proofFiles[$index]->isValid()) {
            $file = $proofFiles[$index];
            $fileName = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/payments'), $fileName);
            $receivable->proof_of_payment = $fileName;
        }

        $receivable->save();
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
