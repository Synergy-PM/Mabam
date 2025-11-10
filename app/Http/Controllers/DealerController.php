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
            'company_name'     => 'nullable|string|max:255',
            'city_id'          => 'nullable|exists:cities,id',
            'opening_balance'  => 'nullable|numeric|min:0',
            'transaction_type' => 'required|in:debit,credit',
            // 'email'         => 'nullable|email|unique:dealers,email',
            // 'whatsapp'      => 'nullable|string',
            // 'address'       => 'nullable|string',
        ]);

        $dealer = new Dealer();
        $dealer->dealer_name     = $request->dealer_name;
        $dealer->company_name    = $request->company_name;
        $dealer->city_id         = $request->city_id;
        $dealer->contact_no      = $request->contact_no; 
        $dealer->opening_balance = $request->opening_balance ?? 0;
        $dealer->transaction_type = $request->transaction_type;
        // $dealer->email = $request->email;
        // $dealer->whatsapp = $request->whatsapp;
        // $dealer->address = $request->address;
        $dealer->save();

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
            'dealer_name'     => 'required|string|max:255',
            'company_name'    => 'nullable|string|max:255',
            'city_id'         => 'nullable|exists:cities,id',
            'opening_balance' => 'nullable|numeric|min:0',
            'transaction_type' => 'required|in:debit,credit',
            // 'email'         => 'nullable|email|unique:dealers,email,' . $dealer->id,
            // 'whatsapp'      => 'nullable|string',
            // 'address'       => 'nullable|string',
        ]);

        $dealer->dealer_name     = $request->dealer_name;
        $dealer->company_name    = $request->company_name;
        $dealer->city_id         = $request->city_id;
        $dealer->contact_no      = $request->contact_no; 
        $dealer->opening_balance = $request->opening_balance ?? 0;
        $dealer->transaction_type = $request->transaction_type;
        // $dealer->email = $request->email;
        // $dealer->whatsapp = $request->whatsapp;
        // $dealer->address = $request->address;
        $dealer->save();

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
