<?php

namespace App\Http\Controllers;

use App\Models\Payable;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Dealer;
use App\Models\Receivable;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $payables = Payable::count();
        $receivable = Receivable::count();
        $totalSupplier = Supplier::count();
        $totalDealer = Dealer::count();
        $pageTitle = 'Dashboard';

        return view('admin.dashboard', compact('totalUsers', 'payables', 'totalSupplier', 'totalDealer', 'pageTitle','receivable'));
    }
}

