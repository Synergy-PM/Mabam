<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Dealer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalCity = City::count();
        $totalSupplier = Supplier::count();
        $totalDealer = Dealer::count();
        return view('admin.dashboard', compact('totalUsers', 'totalCity', 'totalSupplier', 'totalDealer'));
    }
}
