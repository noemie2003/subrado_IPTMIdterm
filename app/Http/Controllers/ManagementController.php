<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Transaction;
use Spatie\Browsershot\Browsershot;

class ManagementController extends Controller
{
    public function soaGeneration()
    {
        $accounts = $this->getAccountsForSOA();

        return view('soa.index',[
            'accounts' => $accounts
        ]);
    }

    public function generateAllSOAs()
    {
        $accounts = $this->getAccountsForSOA();

        foreach ($accounts as $account) {

            Log::info("Generating SOA for Account ID: {$account->id}, Account Number: {$account->account_number}");

            $mail = new \App\Mail\StatementOfAccountMail($account);
            Mail::to($account->customer->email)->queue($mail);

        }

        return redirect()->route('soa.index')->with('status', 'All SOAs have been generated successfully.');
    }

    public function reportsIndex()
    {
        $year = now()->year;

        return view('reports.index', [
            'year' => $year
        ]);
    }

    public function generateMonthlyReportPdf($year, $month)
    {
        $transactions = Transaction::with('account.customer')
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->orderBy('transaction_date')
            ->get();

        $monthName = \Carbon\Carbon::createFromDate($year, $month, 1)->format('F');

        $html = view('reports.monthly-pdf', compact('transactions', 'year', 'month', 'monthName'))->render();

        try {
            $pdf = Browsershot::html($html)
                ->showBackground()
                ->format('A4')
                ->pdf();

            $filename = "transactions_{$year}_{$month}.pdf";

            return response($pdf, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");

        } catch (\Throwable $e) {
            Log::error('Failed generating monthly report PDF: '.$e->getMessage());
            return redirect()->route('reports.index')->with('error', 'Failed to generate PDF.');
        }
    }

    public function reportsShow($year, $month)
    {
        $transactions = Transaction::with(['account.customer', 'processedBy'])
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->orderBy('transaction_date')
            ->get();

        $monthName = \Carbon\Carbon::createFromDate($year, $month, 1)->format('F');

        $total = $transactions->sum('amount');
        $count = $transactions->count();

        return view('reports.monthly', compact('transactions', 'year', 'month', 'monthName', 'total', 'count'));
    }

    private function getAccountsForSOA()
    {
        // return Account::whereDay('start_date', 23)->get();
        return Account::whereDay('start_date', \Carbon\Carbon::now()->addDays(10)->day)->get();
    }
}
