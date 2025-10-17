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
        $suppliers = Supplier::with('city')->latest()->paginate(10);
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
            'supplier_name' => 'required|string|max:255',
            // 'company_name'  => 'required|string|max:255',
            // 'city_id'       => 'nullable|exists:cities,id',
            // 'email'         => 'nullable|email',
            // 'whatsapp'      => 'nullable|string',
            // 'address'       => 'nullable|string',
            // 'contact_person'=> 'nullable|string',
            // 'contact_no'    => 'nullable|string',
            // 'contact_email' => 'nullable|email',
        ]);

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')->with('success', 'Supplier added successfully!');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $cities = City::all();
        return view('admin.suppliers.edit', compact('supplier', 'cities'));
    }

    public function update(Request $request,$id)
    {
        $supplier = Supplier::findOrFail($id);
        
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            // 'company_name'  => 'required|string|max:255',
            // 'city_id'       => 'nullable|exists:cities,id',
            // 'email'         => 'nullable|email',
            // 'whatsapp'      => 'nullable|string',
            // 'address'       => 'nullable|string',
            // 'contact_person'=> 'nullable|string',
            // 'contact_no'    => 'nullable|string',
            // 'contact_email' => 'nullable|email',
        ]);

        $supplier->update($request->all());

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
