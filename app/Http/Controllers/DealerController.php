<?php

namespace App\Http\Controllers;

use App\Models\Dealer;
use App\Models\City;
use Illuminate\Http\Request;

class DealerController extends Controller
{
    public function index()
    {
        $dealers = Dealer::latest()->get();
        $trashDealers = Dealer::onlyTrashed()->count();
        return view('admin.dealers.index', compact('dealers', 'trashDealers'));
    }

    public function create()
    {
        $cities = City::all();
        return view('admin.dealers.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            // 'email' => 'nullable|email|unique:dealers,email',
            // 'whatsapp' => 'nullable|string',
            // 'address' => 'nullable|string',
        ]);

        Dealer::create($request->all());
        return redirect()->route('dealers.index')->with('success', 'Dealer created successfully.');
    }

    public function edit($id)
    {
        $dealer = Dealer::findOrFail($id);
        $cities = City::all();
        return view('admin.dealers.edit', compact('dealer','cities'));
    }

    public function update(Request $request, $id)
    {
         $dealer = Dealer::findOrFail($id);
         $request->validate([
            'dealer_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            // 'email' => 'nullable|email|unique:dealers,email,' . $dealer->id,
            // 'whatsapp' => 'nullable|string',
            // 'address' => 'nullable|string',
        ]);

        $dealer->update($request->all());
        return redirect()->route('dealers.index')->with('success', 'Dealer updated successfully.');
    }

    public function destroy($id)
    {
        $dealer = Dealer::findOrFail($id);
        $dealer->delete();
        return redirect()->route('dealers.index')->with('success', 'Dealer deleted successfully.');
    }

    public function trash()
    {
        $dealers = Dealer::onlyTrashed()->get();
        return view('admin.dealers.trash', compact('dealers'));
    }

    public function restore($id)
    {
        $dealer = Dealer::onlyTrashed()->findOrFail($id);
        $dealer->restore();
        return redirect()->route('dealers.index')->with('success', 'Dealer restored successfully.');
    }
}
