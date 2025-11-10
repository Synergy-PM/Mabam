<?php

namespace App\Http\Controllers;

use App\Models\PayablePayment;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Dealer;
use App\Models\ReceivablePayment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalSupplier = Supplier::count();
        $totalDealer = Dealer::count();
        $pageTitle = 'Dashboard';

        $suppliers = Supplier::with(['payablePayments' => function ($q) {
            $q->orderBy('transaction_date', 'asc');
        }])->get();

        $supplierSummaries = [];

        foreach ($suppliers as $supplier) {
            $payables = $supplier->payablePayments;
            $totalCredit = $payables->where('transaction_type', 'credit')->sum('amount');
            $totalDebit  = $payables->where('transaction_type', 'debit')->sum('amount');

            if ($supplier->transaction_type === 'credit') {
                $totalCredit += $supplier->opening_balance ?? 0;
            } else {
                $totalDebit += $supplier->opening_balance ?? 0;
            }

            $supplierSummaries[] = [
                'supplier_name'   => $supplier->supplier_name,
                'total_credit'    => $totalCredit,
                'total_debit'     => $totalDebit,
                'closing_balance' => $totalCredit - $totalDebit,
            ];
        }
        $totalPayables = collect($supplierSummaries)->sum('closing_balance');
        $dealers = Dealer::with(['receivablePayments' => function ($q) {
            $q->orderBy('transaction_date', 'asc');
        }])->get();

        $dealerSummaries = [];

        foreach ($dealers as $dealer) {
            $receivables = $dealer->receivablePayments;
            $totalCredit = $receivables->where('transaction_type', 'credit')->sum('amount_received');
            $totalDebit  = $receivables->where('transaction_type', 'debit')->sum('amount_received');

            if ($dealer->transaction_type === 'credit') {
                $totalCredit += $dealer->opening_balance ?? 0;
            } else {
                $totalDebit += $dealer->opening_balance ?? 0;
            }

            $dealerSummaries[] = [
                'dealer_name'     => $dealer->dealer_name,
                'total_credit'    => $totalCredit,
                'total_debit'     => $totalDebit,
                'closing_balance' => $totalCredit - $totalDebit,
            ];
        }

        $totalReceivables = collect($dealerSummaries)->sum('closing_balance');

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalPayables',
            'totalReceivables',
            'totalSupplier',
            'totalDealer',
            'pageTitle'
        ));
    }
}
