<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PayablePaymentController;
use App\Http\Controllers\ReceivablePaymentController;
use App\Http\Controllers\BiltiReportController;
use App\Http\Controllers\ChequeBookController;


Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    Route::get('/admin/editpassword', [AuthController::class, 'EditPassword'])->name('EditPassword');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

     Route::controller(RoleController::class)->prefix('role')->group(function () {
        Route::get('/', 'index')->name('role.index');
        Route::get('create', 'create')->name('role.create');
        Route::post('store', 'store')->name('role.store');
        Route::get('edit/{id}', 'edit')->name('role.edit');
        Route::delete('delete/{id}', 'destroy')->name('role.delete');
        Route::put('update/{id}', 'update')->name('role.update');
        Route::get('trash', 'trash')->name('role.trash');
        Route::put('restore/{id}', 'restore')->name('role.restore');
    });

     Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::get('/', 'index')->name('user.index');
        Route::get('create', 'create')->name('user.create');
        Route::post('store', 'store')->name('user.store');
        Route::get('edit/{id}', 'edit')->name('user.edit');
        Route::delete('delete/{id}', 'destroy')->name('user.delete');
        Route::put('update/{id}', 'update')->name('user.update');
        Route::get('trash', 'trash')->name('user.trash');
        Route::get('restore/{id}', 'restore')->name('user.restore');
    });

    Route::controller(UserActivityController::class)->prefix('user_activity')->group(function () {
        Route::get('/', 'index')->name('user_activity.index');
    });



    Route::controller(CityController::class)->prefix('cities')->group(function () {
        Route::get('/', 'index')->name('cities.index');
        Route::get('create', 'create')->name('cities.create');
        Route::post('store', 'store')->name('cities.store');
        Route::get('edit/{id}', 'edit')->name('cities.edit');
        Route::put('update/{id}', 'update')->name('cities.update');
        Route::delete('delete/{id}', 'destroy')->name('cities.delete');
        Route::get('trash', 'trash')->name('cities.trash');
        Route::get('restore/{id}', 'restore')->name('cities.restore');
    });


    Route::controller(DealerController::class)->prefix('dealers')->group(function () {
        Route::get('/', 'index')->name('dealers.index');
        Route::get('create', 'create')->name('dealers.create');
        Route::post('store', 'store')->name('dealers.store');
        Route::get('edit/{id}', 'edit')->name('dealers.edit');
        Route::put('update/{id}', 'update')->name('dealers.update');
        Route::delete('delete/{id}', 'destroy')->name('dealers.delete');
        Route::get('trash', 'trash')->name('dealers.trash');
        Route::get('restore/{id}', 'restore')->name('dealers.restore');
    });

     Route::controller(SupplierController::class)->prefix('suppliers')->group(function () {
        Route::get('/', 'index')->name('suppliers.index');
        Route::get('create', 'create')->name('suppliers.create');
        Route::post('store', 'store')->name('suppliers.store');
        Route::get('edit/{id}', 'edit')->name('suppliers.edit');
        Route::put('update/{id}', 'update')->name('suppliers.update');
        Route::delete('delete/{id}', 'destroy')->name('suppliers.delete');
        Route::get('trash', 'trash')->name('suppliers.trash');
        Route::get('restore/{id}', 'restore')->name('suppliers.restore');
    });

   Route::controller(PayableController::class)->prefix('payables')->group(function () {
        Route::get('/', 'index')->name('payables.index');
        // Route::get('/one', 'show')->name('payables.show');
        Route::get('create', 'create')->name('payables.create');
        // Route::get('create1', 'create1')->name('payables.create1');
        Route::post('store', 'store')->name('payables.store');
        Route::get('edit/{id}', 'edit')->name('payables.edit');
        Route::put('update/{id}', 'update')->name('payables.update');
        Route::delete('delete/{id}', 'destroy')->name('payables.delete');
        Route::get('trash', 'trash')->name('payables.trash');
        Route::get('restore/{id}', 'restore')->name('payables.restore');
    });

 Route::controller(PayablePaymentController::class)
    ->prefix('payable-payments')
    ->group(function () {
        Route::get('/', 'index')->name('payable-payments.index');
        Route::get('create', 'create')->name('payable-payments.create');
        Route::post('/', 'store')->name('payable-payments.store'); 
        Route::get('edit/{id}', 'edit')->name('payable-payments.edit');
        Route::put('{id}', 'update')->name('payable-payments.update');
        Route::delete('{id}', 'destroy')->name('payable-payments.delete');
        Route::get('trash', 'trash')->name('payable-payments.trash');
        Route::put('restore/{id}', 'restore')->name('payable-payments.restore');
        Route::get('ledger-filter', 'ledgerFilter')->name('payable-payments.ledger-filter');
        Route::get('ledger-report', 'ledgerReport')->name('payable-payments.ledger-report');
        Route::get('summary', 'supplierSummary')->name('payable-payments.supplier-summary');
    });


    Route::controller(ExpenseController::class)->prefix('expenses')->group(function () {
        Route::get('/', 'index')->name('expenses.index');
        Route::get('create', 'create')->name('expenses.create');
        Route::post('store', 'store')->name('expenses.store');
        Route::get('edit/{id}', 'edit')->name('expenses.edit');
        Route::put('update/{id}', 'update')->name('expenses.update');
        Route::delete('delete/{id}', 'destroy')->name('expenses.delete');
        Route::get('trash', 'trash')->name('expenses.trash');
        Route::get('restore/{id}', 'restore')->name('expenses.restore');
    });



    Route::controller(BiltiReportController::class)->prefix('bilti')->group(function () {
        Route::get('/report/filter', 'showFilter')->name('bilti.report.filter');
        Route::get('/report', 'index')->name('bilti.report');
    });

    Route::controller(BiltiReportController::class)->prefix('daily')->group(function () {
        Route::get('/report/filter', 'showDailyReportFilter')->name('daily.report.filter');
        Route::get('/report', 'dailyReport')->name('daily.report');
        Route::get('/profit/filter', 'filter')->name('profit.filter');
        Route::get('/profit/report', 'profitReport')->name('profit.report');
    });

Route::controller(ReceivablePaymentController::class)->prefix('receivable-payments')->group(function () {
    Route::get('/', 'index')->name('receivable-payments.index');
    Route::get('create', 'create')->name('receivable-payments.create');
    Route::post('store', 'store')->name('receivable-payments.store');
    Route::get('edit/{id}', 'edit')->name('receivable-payments.edit');
    Route::put('update/{id}', 'update')->name('receivable-payments.update');
    Route::delete('delete/{id}', 'destroy')->name('receivable-payments.delete');
    Route::get('trash', 'trash')->name('receivable-payments.trash');
    Route::post('restore/{id}', 'restore')->name('receivable-payments.restore');
    Route::get('ledger-report-filter', 'ledgerReportFilter')->name('receivable-payments.ledger-report-filter');
    Route::get('ledger-report', 'ledgerReport')->name('receivable-payments.ledger-report');
    Route::get('summary', 'supplierSummary')->name('receivable-payments.supplier-summary');

});

Route::controller(ChequeBookController::class)
    ->prefix('cheque')
    ->group(function () {
        Route::get('/index', 'index')->name('cheque.index');
        Route::get('/create', 'create')->name('cheque.create');
        Route::post('/store', 'store')->name('cheque.store');
        Route::get('/edit/{id}', 'edit')->name('cheque.edit');
        Route::put('/update/{id}', 'update')->name('cheque.update');
        Route::delete('/destroy/{id}', 'destroy')->name('cheque.destroy');
         Route::get('/print', 'print')->name('cheque.print');
    });


});
 