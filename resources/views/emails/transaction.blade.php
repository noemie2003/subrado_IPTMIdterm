<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #4f46e5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .transaction-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #e5e7eb;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #6b7280;
        }
        .detail-value {
            font-weight: 500;
        }
        .amount {
            font-size: 1.2em;
            font-weight: bold;
        }
        .amount.payment {
            color: #10b981;
        }
        .amount.disbursement {
            color: #ef4444;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 0.9em;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Transaction Notification</h1>
    </div>
    
    <div class="content">
        <p>Hello {{ $transaction->account->customer->name }},</p>
        
        <p>A new transaction has been processed on your account. Here are the details:</p>
        
        <div class="transaction-details">
            <div class="detail-row">
                <span class="detail-label">Transaction Number:</span>
                <span class="detail-value">{{ $transaction->transaction_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Transaction Type:</span>
                <span class="detail-value">{{ ucfirst($transaction->type) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount:</span>
                <span class="detail-value amount {{ $transaction->type }}">
                    {{ $transaction->type === 'payment' ? '-' : '+' }} ${{ number_format($transaction->amount, 2) }}
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Transaction Date:</span>
                <span class="detail-value">{{ $transaction->transaction_date->format('M d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Balance After:</span>
                <span class="detail-value">${{ number_format($transaction->balance_after, 2) }}</span>
            </div>
            @if($transaction->payment_method)
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">{{ $transaction->payment_method }}</span>
            </div>
            @endif
            @if($transaction->reference_number)
            <div class="detail-row">
                <span class="detail-label">Reference Number:</span>
                <span class="detail-value">{{ $transaction->reference_number }}</span>
            </div>
            @endif
            @if($transaction->notes)
            <div class="detail-row">
                <span class="detail-label">Notes:</span>
                <span class="detail-value">{{ $transaction->notes }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Processed By:</span>
                <span class="detail-value">{{ $transaction->processedBy->name ?? 'System' }}</span>
            </div>
        </div>
        
        @if($transaction->balance_after <= 0)
        <div style="background: #10b981; color: white; padding: 15px; border-radius: 8px; text-align: center; margin: 20px 0;">
            <strong>🎉 Congratulations! Your account has been fully paid.</strong>
        </div>
        @endif
        
        <p>If you have any questions about this transaction, please contact our support team.</p>
        
        <p>Thank you for your business!</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>