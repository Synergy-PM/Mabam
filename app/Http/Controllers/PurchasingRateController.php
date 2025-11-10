<?php

namespace App\Http\Controllers;

use App\Models\PurchasingRate;
use App\Models\Supplier;
use App\Models\City;
use Illuminate\Http\Request;

class PurchasingRateController extends Controller
{
    public function index()
    {
        $rates = PurchasingRate::with(['supplier', 'city'])
                    ->orderBy('id', 'desc')
                    ->get();

        return view('admin.purchasing_rates.index', compact('rates'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $cities = City::all();
        return view('admin.purchasing_rates.create', compact('suppliers', 'cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'supplier_id' => 'required|exists:suppliers,id',
            'city_id' => 'required|exists:cities,id',
            'amount_per_ton' => 'required|numeric|min:0',
        ]);

        PurchasingRate::create($request->all());

        return redirect()->route('purchasing_rates.index')->with('success', 'Purchasing rate added successfully.');
    }

    public function edit($id)
    {
        $purchasingRate = PurchasingRate::findOrFail($id);
        $suppliers = Supplier::all();
        $cities = City::all();

        return view('admin.purchasing_rates.edit', compact('purchasingRate', 'suppliers', 'cities'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'supplier_id' => 'required|exists:suppliers,id',
            'city_id' => 'required|exists:cities,id',
            'amount_per_ton' => 'required|numeric|min:0',
        ]);

        $purchasing_rate = PurchasingRate::findOrFail($id);
        $purchasing_rate->update($request->all());

        return redirect()->route('purchasing_rates.index')->with('success', 'Purchasing rate updated successfully.');
    }

    public function destroy($id)
    {
        $purchasing_rate = PurchasingRate::findOrFail($id);
        $purchasing_rate->delete();

        return redirect()->route('purchasing_rates.index')->with('success', 'Purchasing rate moved to trash.');
    }

    public function trash()
    {
        $trashedRates = PurchasingRate::onlyTrashed()
                    ->with(['supplier', 'city'])
                    ->orderBy('id', 'desc')
                    ->get();

        return view('admin.purchasing_rates.trash', compact('trashedRates'));
    }

    public function restore($id)
    {
        $rate = PurchasingRate::onlyTrashed()->findOrFail($id);
        $rate->restore();

        return redirect()->route('purchasing_rates.trash')->with('success', 'Purchasing rate restored successfully.');
    }
}
