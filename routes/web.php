<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return view('emails.soa', [
        'account' => \App\Models\Account::first()
    ]);
});

Route::get('/dashboard', function () {
    $totalCustomers = \App\Models\Customer::count();
    $totalAccounts = \App\Models\Account::count();
    $activeAccounts = \App\Models\Account::where('status', 'active')->count();
    $totalPrincipal = \App\Models\Account::sum('principal_amount');
    $totalBalance = \App\Models\Account::sum('balance');
    $recentTransactions = \App\Models\Transaction::with('account.customer')->latest()->take(5)->get();

    return view('dashboard', compact(
        'totalCustomers',
        'totalAccounts',
        'activeAccounts',
        'totalPrincipal',
        'totalBalance',
        'recentTransactions'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customer routes
    Route::resource('customers', CustomerController::class);

    // Account routes
    Route::resource('accounts', AccountController::class);

    // Transaction routes
    Route::resource('transactions', TransactionController::class);

    // SOA routes
    Route::get('/soa', [ManagementController::class, 'soaGeneration'])->name('soa.index');
    Route::get('/soa/generate-all', [ManagementController::class, 'generateAllSOAs'])->name('soa.generateAll');
    // Reports
    Route::get('/reports', [ManagementController::class, 'reportsIndex'])->name('reports.index');
    Route::get('/reports/{year}/{month}', [ManagementController::class, 'reportsShow'])->name('reports.show');
    Route::get('/reports/generate/{year}/{month}', [ManagementController::class, 'generateMonthlyReportPdf'])->name('reports.generate');
});

require __DIR__.'/auth.php';
