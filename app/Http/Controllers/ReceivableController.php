<?php
namespace App\Http\Controllers;

use App\Models\Receivable;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    public function index()
    {
        $receivables = Receivable::latest()->paginate(10);
        $trashCount = Receivable::onlyTrashed()->count();
        return view('admin.receivables.index', compact('receivables','trashCount'));
    }

    public function create()
    {
        return view('admin.receivables.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'transaction_date' => 'required|date',
            'bilti_no'        => 'required|string',
            'truck_no'        => 'required|string',
            'no_of_bags'      => 'required|integer|min:0',
            'amount_per_bag'  => 'required|numeric|min:0',
        ]);

        $data['total_amount'] = $data['no_of_bags'] * $data['amount_per_bag'];

        Receivable::create($data);

        return redirect()->route('receivables.index')->with('success','Receivable created.');
    }

    public function edit(Receivable $receivable)
    {
        return view('admin.receivables.edit', compact('receivable'));
    }

    public function update(Request $request, Receivable $receivable)
    {
        $data = $request->validate([
            'transaction_date' => 'required|date',
            'bilti_no'        => 'required|string',
            'truck_no'        => 'required|string',
            'no_of_bags'      => 'required|integer|min:0',
            'amount_per_bag'  => 'required|numeric|min:0',
        ]);

        $data['total_amount'] = $data['no_of_bags'] * $data['amount_per_bag'];

        $receivable->update($data);

        return redirect()->route('receivables.index')->with('success','Receivable updated.');
    }

    public function destroy(Receivable $receivable)
    {
        $receivable->delete();
        return redirect()->route('receivables.index')->with('success','Moved to trash.');
    }

    public function trash()
    {
        $receivables = Receivable::onlyTrashed()->paginate(10);
        return view('admin.receivables.trash', compact('receivables'));
    }

    public function restore($id)
    {
        Receivable::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('receivables.trash')->with('success','Restored.');
    }

    public function forceDelete($id)
    {
        Receivable::onlyTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('receivables.trash')->with('success','Permanently deleted.');
    }
}
