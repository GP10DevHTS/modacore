<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">

        <div class="rounded-xl border border-red-200 bg-red-50 p-5 shadow-sm dark:border-red-800/30 dark:bg-red-900/10">
            <div class="flex size-8 items-center justify-center rounded-lg bg-red-100 dark:bg-red-800/30">
                <svg class="size-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-red-700 dark:text-red-400">Total Billed</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-red-700 dark:text-red-300">UGX {{ number_format($this->totalBilled, 0) }}</p>
            <p class="mt-1 text-xs text-red-600/60 dark:text-red-500">All expense bills</p>
        </div>

        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-800/30 dark:bg-emerald-900/10">
            <div class="flex size-8 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-800/30">
                <svg class="size-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-emerald-700 dark:text-emerald-400">Total Paid</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-emerald-700 dark:text-emerald-300">UGX {{ number_format($this->totalPaid, 0) }}</p>
            <p class="mt-1 text-xs text-emerald-600/60 dark:text-emerald-500">Payments made</p>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-800/30 dark:bg-amber-900/10">
            <div class="flex size-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-800/30">
                <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-amber-700 dark:text-amber-400">Outstanding</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-amber-700 dark:text-amber-300">UGX {{ number_format($this->outstanding, 0) }}</p>
            <p class="mt-1 text-xs text-amber-600/60 dark:text-amber-500">{{ $this->unpaidCount }} unpaid {{ Str::plural('bill', $this->unpaidCount) }}</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                <svg class="size-4 text-zinc-600 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">This Month</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->thisMonthBilled, 0) }}</p>
            <p class="mt-1 text-xs text-zinc-400">{{ now()->format('F Y') }}</p>
        </div>

    </div>
</div>
