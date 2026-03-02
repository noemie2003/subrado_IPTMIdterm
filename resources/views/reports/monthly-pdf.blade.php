<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transactions - {{ $monthName }} {{ $year }}</title>
    <style>
        @page { margin: 20mm; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #222; margin: 0; }
        .report-header { display:flex; justify-content:space-between; align-items:center; padding: 8px 0; }
        .title { text-align: right; }
        .title h1 { margin:0; font-size:18px; }
        .brand { display:flex; gap:12px; align-items:center; }
        .brand img { height:42px; }
        .summary { margin-top:10px; display:flex; gap:12px; }
        .card { background:#f7f7f9; padding:8px 10px; border-radius:6px; }
        table { width:100%; border-collapse: collapse; margin-top:12px; font-size:11px; }
        th, td { border: 1px solid #e3e3e3; padding:8px 6px; }
        th { background:#f2f4f7; color:#333; font-weight:600; }
        tbody tr:nth-child(odd) { background:#ffffff; }
        tbody tr:nth-child(even) { background:#fbfbfc; }
        tfoot td { font-weight:600; }
        .right { text-align:right; }
        .small { font-size:10px; color:#666; }
        /* Prevent row breaks inside a single row */
        tr { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="brand">
            <div style="font-weight:700; font-size:14px; color:#2563eb">{{ config('app.name', 'Company') }}</div>
            <div class="small">Transactions Report</div>
        </div>
        <div class="title">
            <h1>{{ $monthName }} {{ $year }}</h1>
            <div class="small">Generated: {{ now()->format('Y-m-d H:i') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px">#</th>
                <th style="width:80px">Date</th>
                <th>Account</th>
                <th>Customer</th>
                <th>Txn No.</th>
                <th>Type</th>
                <th class="right">Amount</th>
                <th>Balance</th>
                <th>Method</th>
                <th>Processed By</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $i => $t)
                <tr>
                    <td class="small">{{ $i + 1 }}</td>
                    <td class="small">{{ optional($t->transaction_date)->format('Y-m-d') }}</td>
                    <td class="small">{{ optional($t->account)->account_number }}</td>
                    <td class="small">{{ optional($t->account->customer)->name }}</td>
                    <td class="small">{{ $t->transaction_number }}</td>
                    <td class="small">{{ ucfirst($t->type) }}</td>
                    <td class="small right">{{ number_format($t->amount, 2) }}</td>
                    <td class="small">{{ number_format($t->balance_after, 2) }}</td>
                    <td class="small">{{ $t->payment_method }}</td>
                    <td class="small">{{ optional($t->processedBy)->name }}</td>
                    <td class="small">{{ $t->reference ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e3e3e3;">
        <table style="width: 100%; margin-top: 15px;">
            <tr>
                <td style="text-align: center; padding: 8px 6px; font-weight: 600; border: 1px solid #e3e3e3;">Total Transactions</td>
                <td style="text-align: center; padding: 8px 6px; font-weight: 600; border: 1px solid #e3e3e3;">Total Amount</td>
                <td style="text-align: center; padding: 8px 6px; font-weight: 600; border: 1px solid #e3e3e3;">Average Amount</td>
            </tr>
            <tr>
                <td style="text-align: center; padding: 12px 6px; font-weight: 600; border: 1px solid #e3e3e3; width: 33.33%;">{{ $transactions->count() }}</td>
                <td style="text-align: center; padding: 12px 6px; font-weight: 600; border: 1px solid #e3e3e3; width: 33.33%;">₱{{ number_format($transactions->sum('amount'), 2) }}</td>
                <td style="text-align: center; padding: 12px 6px; font-weight: 600; border: 1px solid #e3e3e3; width: 33.33%;">₱{{ $transactions->count() ? number_format($transactions->sum('amount') / $transactions->count(), 2) : '0.00' }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
