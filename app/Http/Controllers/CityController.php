<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::latest()->paginate(10);
        $trashCities = City::onlyTrashed()->count();
        return view('admin.cities.index', compact('cities','trashCities'));
    }

    public function create()
    {
        return view('admin.cities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        City::create($request->only('name'));

        return redirect()->route('cities.index')->with('success', 'City created successfully.');
    }

    public function edit(City $city)
    {
        return view('admin.cities.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $city->update($request->only('name'));

        return redirect()->route('cities.index')->with('success', 'City updated successfully.');
    }

    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();
        return redirect()->route('cities.index')->with('success', 'City deleted successfully.');
    }

        public function trash()
    {
        $cities = City::onlyTrashed()->get();
        return view('admin.cities.trash', compact('cities'));
    }

    public function restore($id)
    {
        $city = City::onlyTrashed()->findOrFail($id);
        $city->restore();
        return redirect()->route('cities.index')->with('success', 'City restored successfully.');
    }

}
