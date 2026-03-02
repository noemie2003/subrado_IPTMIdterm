<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Yearly Transactions Report - ') }}{{ $year }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Info Card -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800 dark:text-blue-300">Select a month to view detailed transactions and download a PDF report.</p>
            </div>

            <!-- Month Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @for($m=1;$m<=12;$m++)
                    <a href="{{ route('reports.show', ['year' => $year, 'month' => $m]) }}" class="block">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                            <div class="p-6 text-center">
                                <div class="bg-indigo-100 dark:bg-indigo-900 p-3 rounded-full mx-auto w-fit mb-3">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::createFromDate($year, $m, 1)->format('F') }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">View & Download</p>
                            </div>
                        </div>
                    </a>
                @endfor
            </div>
        </div>
    </div>
</x-app-layout>
