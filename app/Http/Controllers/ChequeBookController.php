<?php

namespace App\Http\Controllers;

use App\Models\ChequeEntry;
use App\Models\Supplier;
use App\Models\Dealer;
use App\Models\PayablePayment;
use App\Models\ReceivablePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChequeBookController extends Controller
{

     public function index(Request $request)
    {
        $query = ChequeEntry::orderBy('date', 'asc'); 

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }
        if ($request->filled('party_type')) {
            $query->where('party_type', $request->party_type);
        }

        $entries = $query->get();

        $cashTotal = 0;
        $accTotal = 0;

        foreach ($entries as $entry) {
            $diff = ($entry->credit ?? 0) - ($entry->debit ?? 0);

            if ($entry->payment_type === 'cash') $cashTotal += $diff;
            if (in_array($entry->payment_type, ['online','cheque'])) $accTotal += $diff;
        }

        return view('admin.cheque-book.index', compact('entries', 'cashTotal','accTotal'));
    }


    public function create()
    {
        $suppliers = Supplier::select('id', 'supplier_name as name')->get();
        $dealers = Dealer::select('id', 'dealer_name as name')->get();

        $parties = collect();

        foreach ($suppliers as $s) {
            $parties[$s->id] = [
                'name' => $s->name,
                'type' => 'supplier'
            ];
        }

        foreach ($dealers as $d) {
            $parties['D' . $d->id] = [
                'name' => $d->name,
                'type' => 'dealer'
            ];
        }

        $paymentTypes = [
            'cash' => 'Cash',
            'online' => 'Online',
            'cheque' => 'Cheque'
        ];

        return view('admin.cheque-book.create', [
            'parties' => $parties,
            'paymentTypes' => $paymentTypes
        ]);
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.date' => 'required|date',
            'entries.*.party_type' => 'required|string|in:supplier,dealer,expense',
            'entries.*.party_id' => 'nullable',
            'entries.*.expense_description' => 'nullable|string|max:255',
            'entries.*.credit' => 'nullable|numeric|min:0',
            'entries.*.debit' => 'nullable|numeric|min:0',
            'entries.*.payment_type' => 'nullable|string|in:cash,online,cheque',
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['entries'] as $entry) {

                if (empty($entry['credit']) && empty($entry['debit']) && empty($entry['expense_description'])) {
                    continue;
                }

                // ✅ Define credit and debit first
                $credit = $entry['credit'] ?? 0;
                $debit = $entry['debit'] ?? 0;

                // ✅ Extract party details
                $rawPartyId = $entry['party_id'] ?? null;
                $partyType = $entry['party_type'] ?? null;
                $partyId = null;

                if ($partyType === 'dealer' && is_string($rawPartyId) && str_starts_with($rawPartyId, 'D')) {
                    $partyId = (int) substr($rawPartyId, 1); // removes 'D' prefix
                } elseif ($partyType === 'supplier') {
                    $partyId = (int) $rawPartyId;
                }

                $isExpense = $partyType === 'expense';

                if ($credit > 0 && $debit > 0) {
                    throw new \Exception("Cannot have both Credit and Debit in the same entry.");
                }

                // ✅ Save cheque entry
                $cheque = ChequeEntry::create([
                    'date' => $entry['date'],
                    'party_type' => $partyType,
                    'party_id' => $isExpense ? null : $partyId,
                    'expense_description' => $isExpense ? ($entry['expense_description'] ?? null) : null,
                    'credit' => $credit,
                    'debit' => $debit,
                    'payment_type' => $isExpense ? null : ($entry['payment_type'] ?? null),
                ]);

                // ✅ Payable (Supplier)
                if ($partyType === 'supplier' && $partyId) {
                    if ($credit > 0) {
                        // Paying to supplier → supplier payable decreases → CREDIT
                        PayablePayment::create([
                            'supplier_id' => $partyId,
                            'transaction_date' => $entry['date'],
                            'transaction_type' => 'credit',
                            'amount' => $credit,
                            'payment_mode' => $entry['payment_type'] ?? null,
                        ]);
                    } elseif ($debit > 0) {
                        // Supplier refund → payable increases → DEBIT
                        PayablePayment::create([
                            'supplier_id' => $partyId,
                            'transaction_date' => $entry['date'],
                            'transaction_type' => 'debit',
                            'amount' => $debit,
                            'payment_mode' => $entry['payment_type'] ?? null,
                        ]);
                    }
                }

                // ✅ Receivable (Dealer)
                if ($partyType === 'dealer' && $partyId) {
                    if ($credit > 0) {
                        // Money received from dealer
                        ReceivablePayment::create([
                            'dealer_id' => $partyId,
                            'transaction_date' => $entry['date'],
                            'transaction_type' => 'debit', // dealer receivable decreases
                            'amount_received' => $credit,
                            'payment_mode' => $entry['payment_type'] ?? null,
                        ]);
                    } elseif ($debit > 0) {
                        // Money paid to dealer
                        ReceivablePayment::create([
                            'dealer_id' => $partyId,
                            'transaction_date' => $entry['date'],
                            'transaction_type' => 'credit', // dealer receivable increases
                            'amount_received' => $debit,
                            'payment_mode' => $entry['payment_type'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('cheque.index')->with('success', 'Cash Book entries saved successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            dd('Error:', $th->getMessage());
            return back()->with('error', 'Failed to save entries: ' . $th->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $entries  = ChequeEntry::where('id', $id)->get();
        if (!$entries) {
            $entries = ChequeEntry::all()->get();
        }

        $suppliers = Supplier::select('id', 'supplier_name as name')->get();
        $dealers = Dealer::select('id', 'dealer_name as name')->get();

        $parties = collect();
        foreach ($suppliers as $s) {
            $parties[$s->id] = [
                'name' => $s->name,
                'type' => 'supplier'
            ];
        }
        foreach ($dealers as $d) {
            $parties['D' . $d->id] = [
                'name' => $d->name,
                'type' => 'dealer'
            ];
        }

        $paymentTypes = [
            'cash' => 'Cash',
            'online' => 'Online',
            'cheque' => 'Cheque'
        ];

        return view('admin.cheque-book.edit', [
            'entries' => $entries,
            'parties' => $parties,
            'paymentTypes' => $paymentTypes,
            'entry' => $entries->first() // For the form action route
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.date' => 'required|date',
            'entries.*.party_type' => 'required|string|in:supplier,dealer,expense',
            'entries.*.party_id' => 'nullable',
            'entries.*.expense_description' => 'nullable|string|max:255',
            'entries.*.credit' => 'nullable|numeric|min:0',
            'entries.*.debit' => 'nullable|numeric|min:0',
            'entries.*.payment_type' => 'nullable|string|in:cash,online,cheque',
        ]);

        DB::beginTransaction();

        try {
            ChequeEntry::where('id', $id)->delete();

            foreach ($validated['entries'] as $entry) {
                if (empty($entry['credit']) && empty($entry['debit']) && empty($entry['expense_description'])) {
                    continue;
                }

                $credit = $entry['credit'] ?? 0;
                $debit = $entry['debit'] ?? 0;

                $rawPartyId = $entry['party_id'] ?? null;
                $partyType = $entry['party_type'] ?? null;
                $partyId = null;

                if ($partyType === 'dealer' && is_string($rawPartyId) && str_starts_with($rawPartyId, 'D')) {
                    $partyId = (int) substr($rawPartyId, 1);
                } elseif ($partyType === 'supplier') {
                    $partyId = (int) $rawPartyId;
                }

                $isExpense = $partyType === 'expense';

                if ($credit > 0 && $debit > 0) {
                    throw new \Exception("Cannot have both Credit and Debit in the same entry.");
                }

                $cheque = ChequeEntry::create([
                    'date' => $entry['date'],
                    'party_type' => $partyType,
                    'party_id' => $isExpense ? null : $partyId,
                    'expense_description' => $isExpense ? ($entry['expense_description'] ?? null) : null,
                    'credit' => $credit,
                    'debit' => $debit,
                    'payment_type' => $isExpense ? null : ($entry['payment_type'] ?? null),
                ]);

                if ($partyType === 'supplier' && $partyId) {
                    if ($credit > 0) {
                        PayablePayment::create([
                            'supplier_id' => $partyId,
                            'transaction_date' => $entry['date'],
                            'transaction_type' => 'credit',
                            'amount' => $credit,
                            'payment_mode' => $entry['payment_type'] ?? null,
                        ]);
                    } elseif ($debit > 0) {
                        PayablePayment::create([
                            'supplier_id' => $partyId,
                            'transaction_date' => $entry['date'],
                            'transaction_type' => 'debit',
                            'amount' => $debit,
                            'payment_mode' => $entry['payment_type'] ?? null,
                        ]);
                    }
                }

                if ($partyType === 'dealer' && $partyId) {
                    if ($credit > 0) {
                        ReceivablePayment::create([
                            'dealer_id' => $partyId,
                            'transaction_date' => $entry['date'],
                            'transaction_type' => 'debit',
                            'amount_received' => $credit,
                            'payment_mode' => $entry['payment_type'] ?? null,
                        ]);
                    } elseif ($debit > 0) {
                        ReceivablePayment::create([
                            'dealer_id' => $partyId,
                            'transaction_date' => $entry['date'],
                            'transaction_type' => 'credit',
                            'amount_received' => $debit,
                            'payment_mode' => $entry['payment_type'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('cheque.index')->with('success', 'Cash Book entries updated successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Failed to update entries: ' . $th->getMessage())->withInput();
        }
    }


    public function destroy($id)
    {
        $entry = ChequeEntry::findOrFail($id);
        $entry->delete();

        return redirect()->route('cheque.index')->with('success', 'Entry deleted successfully.');
    }

   

}