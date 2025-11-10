<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\City;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
   public function index()
{
    $trashSuppliers = Supplier::onlyTrashed()->count();
    $suppliers = Supplier::latest()->get(); 
    return view('admin.suppliers.index', compact('suppliers','trashSuppliers'));
}


    public function create()
    {
        $cities = City::all();
        return view('admin.suppliers.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name'    => 'required|string|max:255',
            'opening_balance'  => 'nullable|numeric|min:0',
            'transaction_type' => 'required|in:debit,credit',
            // 'company_name'  => 'nullable|string|max:255',
            // 'city_id'       => 'nullable|exists:cities,id',
            // 'email'         => 'nullable|email',
            // 'whatsapp'      => 'nullable|string',
            // 'address'       => 'nullable|string',
            // 'contact_person'=> 'nullable|string',
            // 'contact_no'    => 'nullable|string',
            // 'contact_email' => 'nullable|email',
        ]);

        $supplier = new Supplier();
        $supplier->supplier_name = $request->supplier_name;
        $supplier->opening_balance = $request->opening_balance ?? 0;
        $supplier->transaction_type = $request->transaction_type;
        // $supplier->company_name = $request->company_name;
        // $supplier->city_id = $request->city_id;
        // $supplier->email = $request->email;
        // $supplier->whatsapp = $request->whatsapp;
        // $supplier->address = $request->address;
        // $supplier->contact_person = $request->contact_person;
        // $supplier->contact_no = $request->contact_no;
        // $supplier->contact_email = $request->contact_email;
        $supplier->save();

        return redirect()->route('suppliers.index')->with('success', 'Supplier added successfully!');
    }


    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_name'    => 'required|string|max:255',
            'opening_balance'  => 'nullable|numeric|min:0',
            'transaction_type' => 'required|in:debit,credit',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->supplier_name = $request->supplier_name;
        $supplier->opening_balance = $request->opening_balance ?? 0;
        $supplier->transaction_type = $request->transaction_type;
        $supplier->save();

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully!');
    }


    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully!');
    }

     public function trash()
    {
        $suppliers = Supplier::onlyTrashed()->get();
        return view('admin.suppliers.trash', compact('suppliers'));
    }
    public function restore($id)
    {
        $suppliers = Supplier::onlyTrashed()->findOrFail($id);
        $suppliers->restore();
        return redirect()->route('suppliers.index')->with('success', 'Dealer restored successfully.');
    }
}
