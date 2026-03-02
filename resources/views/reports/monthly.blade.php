<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Transactions — {{ $monthName }} {{ $year }}</h2>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('reports.generate', ['year' => $year, 'month' => $month]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-700 text-white text-sm font-medium rounded-lg hover:bg-blue-700 dark:hover:bg-blue-800 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Download PDF
                </a>
                <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">


            <!-- Transactions Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-semibold text-gray-900 dark:text-gray-100">Detailed Transactions</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">All transactions for {{ $monthName }} {{ $year }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Account</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Txn No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Balance</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Processed By</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($transactions as $i => $t)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $i + 1 }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ optional($t->transaction_date)->format('Y-m-d') }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ optional($t->account)->account_number }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-300">{{ optional($t->account->customer)->name }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $t->transaction_number }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $t->type === 'debit' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">{{ ucfirst($t->type) }}</span>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium text-gray-900 dark:text-gray-100">₱{{ number_format($t->amount, 2) }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">₱{{ number_format($t->balance_after, 2) }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $t->payment_method }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ optional($t->processedBy)->name }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $t->reference ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No transactions found for this month.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Totals Row -->
                <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex justify-end space-x-8">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Count:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $count }}</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">₱{{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Average:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">₱{{ $count ? number_format($total / $count, 2) : '0.00' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
