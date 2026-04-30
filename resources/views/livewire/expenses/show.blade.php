<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('expenses.index') }}" wire:navigate
                class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="font-mono text-xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->bill->expense_number }}</h1>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $this->bill->payment_status->pillClasses() }}">
                        <span class="size-1.5 rounded-full {{ $this->bill->payment_status->dotClasses() }}"></span>
                        {{ $this->bill->payment_status->label() }}
                    </span>
                </div>
                <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $this->bill->title }}
                    @if($this->bill->createdBy)
                        &middot; Recorded by {{ $this->bill->createdBy->name }}
                    @endif
                </p>
            </div>
        </div>

        @can('expenses.create')
        @if($this->bill->payment_status !== \App\Enums\PaymentStatus::FullyPaid)
            <a href="{{ route('expenses.pay', $this->bill->id) }}" wire:navigate>
                <flux:button variant="primary" icon="banknotes" size="sm"
>
                    Record Payment
                </flux:button>
            </a>
        @endif
        @endcan
    </div>

    {{-- Summary Strip --}}
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-zinc-200 bg-white px-5 py-4 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Billed</p>
            <p class="mt-1 text-xl font-bold tabular-nums text-zinc-900 dark:text-zinc-100">
                UGX {{ number_format((float) $this->bill->amount, 0) }}
            </p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white px-5 py-4 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Paid</p>
            <p class="mt-1 text-xl font-bold tabular-nums text-emerald-600 dark:text-emerald-400">
                UGX {{ number_format($this->bill->totalPaid(), 0) }}
            </p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white px-5 py-4 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Balance Due</p>
            <p class="mt-1 text-xl font-bold tabular-nums {{ $this->bill->balance() > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-400 dark:text-zinc-500' }}">
                UGX {{ number_format($this->bill->balance(), 0) }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Left: Payments --}}
        <div class="space-y-5 lg:col-span-2">

            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Payment History</h2>
                    @can('expenses.create')
                    @if($this->bill->payment_status !== \App\Enums\PaymentStatus::FullyPaid)
                        <a href="{{ route('expenses.pay', $this->bill->id) }}" wire:navigate>
                            <flux:button variant="ghost" icon="plus" size="xs">Add Payment</flux:button>
                        </a>
                    @endif
                    @endcan
                </div>

                @if($this->bill->payments->isEmpty())
                    <div class="px-5 py-10 text-center">
                        <svg class="mx-auto size-8 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/>
                        </svg>
                        <p class="mt-2 text-sm text-zinc-400 dark:text-zinc-500">No payments recorded yet.</p>
                    </div>
                @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-100 bg-zinc-50/60 dark:border-zinc-700/50 dark:bg-zinc-800/40">
                                <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Date</th>
                                <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Method</th>
                                <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Reference</th>
                                <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">Recorded By</th>
                                <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                            @foreach($this->bill->payments->sortByDesc('payment_date') as $payment)
                            <tr>
                                <td class="px-5 py-3 text-zinc-700 dark:text-zinc-300">{{ $payment->payment_date->format('d M Y') }}</td>
                                <td class="px-5 py-3 capitalize text-zinc-600 dark:text-zinc-400">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                                <td class="px-5 py-3 font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $payment->reference ?? '—' }}</td>
                                <td class="px-5 py-3 text-zinc-500 dark:text-zinc-400">{{ $payment->createdBy?->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-right font-semibold tabular-nums text-emerald-700 dark:text-emerald-400">
                                    UGX {{ number_format((float) $payment->amount, 0) }}
                                </td>
                            </tr>
                            @if($payment->notes)
                            <tr class="bg-zinc-50/40 dark:bg-zinc-800/20">
                                <td colspan="5" class="px-5 pb-3 pt-0 text-xs italic text-zinc-400 dark:text-zinc-500">{{ $payment->notes }}</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-zinc-200 bg-zinc-50/60 dark:border-zinc-700/60 dark:bg-zinc-800/40">
                                <td colspan="4" class="px-5 py-3 text-right text-sm font-semibold text-zinc-900 dark:text-zinc-100">Total Paid</td>
                                <td class="px-5 py-3 text-right font-bold tabular-nums text-emerald-700 dark:text-emerald-400">
                                    UGX {{ number_format($this->bill->totalPaid(), 0) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>

        </div>

        {{-- Right: Bill Details + Financial Summary --}}
        <div class="space-y-5">

            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Bill Details</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Item</dt>
                        <dd class="mt-0.5 font-medium text-zinc-900 dark:text-zinc-100">{{ $this->bill->item?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Category</dt>
                        <dd class="mt-0.5 text-zinc-700 dark:text-zinc-300">{{ $this->bill->item?->category?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Bill Date</dt>
                        <dd class="mt-0.5 text-zinc-700 dark:text-zinc-300">{{ $this->bill->expense_date->format('d M Y') }}</dd>
                    </div>
                    @if($this->bill->reference)
                    <div>
                        <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Reference</dt>
                        <dd class="mt-0.5 font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $this->bill->reference }}</dd>
                    </div>
                    @endif
                    @if($this->bill->notes)
                    <div>
                        <dt class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Notes</dt>
                        <dd class="mt-0.5 text-zinc-600 dark:text-zinc-400">{{ $this->bill->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Financial Summary</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">Billed</span>
                        <span class="font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format((float) $this->bill->amount, 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">Paid</span>
                        <span class="font-semibold tabular-nums text-emerald-600 dark:text-emerald-400">UGX {{ number_format($this->bill->totalPaid(), 0) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-zinc-100 pt-3 dark:border-zinc-700/60">
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">Balance</span>
                        <span class="font-bold tabular-nums {{ $this->bill->balance() > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-400' }}">
                            UGX {{ number_format($this->bill->balance(), 0) }}
                        </span>
                    </div>
                </div>

                @can('expenses.create')
                @if($this->bill->payment_status !== \App\Enums\PaymentStatus::FullyPaid)
                <div class="mt-4">
                    <a href="{{ route('expenses.pay', $this->bill->id) }}" wire:navigate class="block">
                        <flux:button variant="primary" icon="banknotes" class="w-full"
        >
                            Record Payment
                        </flux:button>
                    </a>
                </div>
                @endif
                @endcan
            </div>

        </div>
    </div>

</div>
