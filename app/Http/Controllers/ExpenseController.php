<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::latest()->paginate(10);
        $trashExpenses = Expense::onlyTrashed()->count();
        return view('admin.expenses.index', compact('expenses','trashExpenses'));
    }
    public function create()
    {
        return view('admin.expenses.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'expense_description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        return view('admin.expenses.edit', compact('expense'));
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'expense_date' => 'required|date',
            'expense_description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update($validated);
        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense moved to trash.');
    }
    public function trash()
    {
        $expenses = Expense::onlyTrashed()->paginate(10);
        return view('admin.expenses.trash', compact('expenses'));
    }

    public function restore($id)
    {
        $expense = Expense::onlyTrashed()->findOrFail($id);
        $expense->restore();
        return redirect()->route('expenses.trash')->with('success', 'Expense restored successfully.');
    }
}
