<?php
namespace App\Http\Controllers;

use App\Models\Payable;
use App\Models\Receivable;
use App\Models\Dealer;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PayableController extends Controller
{
    public function index()
    {
        $payables = Payable::with('supplier')->latest()->paginate(10);
        $trashCount = Payable::onlyTrashed()->count();
        return view('admin.Payable.index', compact('payables','trashCount'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $dealers = Dealer::all();
        $biltis = Receivable::all();
        return view('admin.Payable.create', compact('suppliers','biltis','dealers'));
    }

       public function store(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'no_of_bags' => 'required|numeric|min:0',
            'amount_per_bag' => 'required|numeric|min:0',
            'bilti_no' => 'nullable|string',
        ]);

        $no_of_bags = $request->no_of_bags;
        $amount_per_bag = $request->amount_per_bag;

        Payable::create([
            'transaction_date' => $request->transaction_date,
            'supplier_id' => $request->supplier_id,
            'no_of_bags' => $no_of_bags,
            'amount_per_bag' => $amount_per_bag,
            'total_amount' => $no_of_bags * $amount_per_bag,
            'tons' => $no_of_bags / 20,
            'bilti_no' => $request->bilti_no,
        ]);

        return redirect()->back()->with('success', 'Payable saved successfully!');
    }
    public function edit($id)
    {
        $payable = Payable::findOrFail($id);
        $suppliers = Supplier::all();
        return view('admin.Payable.edit', compact('payable','suppliers'));
    }

    public function update(Request $request, $id)
    {
        $payable = Payable::findOrFail($id);
        $data = $request->validate([
            'transaction_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'no_of_bags' => 'required|integer|min:0',
            'amount_per_bag' => 'required|numeric|min:0',
            'bilti_no' => 'nullable|string',
        ]);

        $data['total_amount'] = $data['no_of_bags'] * $data['amount_per_bag'];

        $payable->update($data);

        return redirect()->route('payables.index')->with('success','Payable updated.');
    }

    public function destroy($id)
    {
        $payable = Payable::findOrFail($id);
        $payable->delete();
        return redirect()->route('payables.index')->with('success','Moved to trash.');
    }

    public function trash()
    {
        $payables = Payable::onlyTrashed()->with('supplier')->paginate(10);
        return view('admin.Payable.trash', compact('payables'));
    }

    public function restore($id)
    {
        Payable::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('payables.trash')->with('success','Restored.');
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
