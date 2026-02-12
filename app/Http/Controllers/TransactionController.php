<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Mail\TransactionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index()
    {
        $transactions = Transaction::with('account.customer')->latest()->paginate(20);
        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create()
    {
        $accounts = Account::with('customer')->where('status', 'active')->get();
        return view('transactions.create', compact('accounts'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'transaction_number' => 'required|string|unique:transactions',
            'type' => 'required|in:disbursement,payment',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'payment_method' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $account = Account::findOrFail($validated['account_id']);
            
            // Calculate new balance based on transaction type
            if ($validated['type'] === 'disbursement') {
                // For disbursement, this adds to what customer owes (shouldn't normally happen after account creation)
                $newBalance = $account->balance + $validated['amount'];
            } else {
                // For payment, this reduces what customer owes
                $newBalance = $account->balance - $validated['amount'];
            }

            // Create the transaction
            $transaction = Transaction::create([
                'account_id' => $validated['account_id'],
                'transaction_number' => $validated['transaction_number'],
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'transaction_date' => $validated['transaction_date'],
                'balance_after' => $newBalance,
                'payment_method' => $validated['payment_method'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'processed_by' => Auth::id(),
            ]);

            // Update account balance
            $account->balance = $newBalance;
            
            // Update account status if fully paid
            if ($newBalance <= 0) {
                $account->status = 'paid';
            }
            
            $account->save();

            // Send transaction email notification
            if ($account->customer && $account->customer->email) {
                Mail::to($account->customer->email)->send(new TransactionMail($transaction));
            }
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load('account.customer', 'processedBy');
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction)
    {
        $accounts = Account::with('customer')->get();
        return view('transactions.edit', compact('transaction', 'accounts'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'payment_method' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            $account = $transaction->account;
            
            // Reverse the transaction effect on account balance
            if ($transaction->type === 'disbursement') {
                $account->balance -= $transaction->amount;
            } else {
                $account->balance += $transaction->amount;
            }
            
            // Update account status
            if ($account->balance > 0 && $account->status === 'paid') {
                $account->status = 'active';
            }
            
            $account->save();
            
            // Delete the transaction
            $transaction->delete();
        });

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }
}