<div class="space-y-6">

    @php
        $statusConfig = [
            'draft'     => ['pill' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400', 'dot' => 'bg-zinc-400', 'label' => 'Draft'],
            'confirmed' => ['pill' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400', 'dot' => 'bg-blue-500', 'label' => 'Confirmed'],
            'active'    => ['pill' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400', 'dot' => 'bg-emerald-500 animate-pulse', 'label' => 'Active'],
            'completed' => ['pill' => 'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400', 'dot' => 'bg-violet-500', 'label' => 'Completed'],
            'cancelled' => ['pill' => 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400', 'dot' => 'bg-red-500', 'label' => 'Cancelled'],
        ];
        $cfg = $statusConfig[$booking->status] ?? $statusConfig['draft'];
        $itemStatusConfig = [
            'pending'     => ['pill' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400', 'label' => 'Pending'],
            'checked_out' => ['pill' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400', 'label' => 'Checked Out'],
            'in_cleaning' => ['pill' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/20 dark:text-sky-400', 'label' => 'In Cleaning'],
            'returned'    => ['pill' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400', 'label' => 'Returned'],
        ];
    @endphp

    {{-- Page Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('bookings.index') }}" wire:navigate
                class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:bg-zinc-50 hover:text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-mono text-xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $booking->booking_number }}</h1>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $cfg['pill'] }}">
                        <span class="size-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                        {{ $cfg['label'] }}
                    </span>
                </div>
                <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $booking->customer->name }} &middot;
                    {{ $booking->hire_from->format('d M Y, H:i') }} → {{ $booking->hire_to->format('d M Y, H:i') }}
                </p>
            </div>
        </div>

        {{-- Status Actions --}}
        <div class="flex flex-wrap items-center gap-2">
            @can('bookings.edit')
            @if($booking->status === 'draft')
                <a href="{{ route('bookings.edit', $booking->id) }}" wire:navigate
                    class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-sm font-medium text-zinc-600 shadow-sm hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <button wire:click="transitionStatus('confirmed')"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-blue-500 transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Confirm
                </button>
            @endif
            @if($booking->status === 'confirmed')
                <button wire:click="transitionStatus('active')"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-emerald-500 transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Mark Active
                </button>
            @endif
            @if($booking->status === 'active')
                <button wire:click="transitionStatus('completed')"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-violet-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-violet-500 transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Mark Completed
                </button>
            @endif
            @if(! in_array($booking->status, ['cancelled', 'completed']))
                <button wire:click="transitionStatus('cancelled')"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3.5 py-2 text-sm font-semibold text-red-600 hover:bg-red-100 dark:border-red-800/30 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30 transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancel
                </button>
            @endif
            @endcan
            @can('payments.create')
            @if($booking->status !== 'cancelled')
                <button wire:click="openPaymentForm"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-black hover:bg-amber-400 transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Record Payment
                </button>
            @endif
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Left Column --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Items with Tracking --}}
            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Hired Items</h2>
                    <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $booking->items->count() }} {{ Str::plural('item', $booking->items->count()) }}</span>
                </div>

                <div class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                    @forelse ($booking->items as $item)
                        @php $icfg = $itemStatusConfig[$item->status] ?? $itemStatusConfig['pending']; @endphp
                        <div class="flex items-start justify-between gap-4 px-5 py-4" wire:key="item-{{ $item->id }}">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $item->inventoryItem->name }}</span>
                                    @if($item->variant)
                                        <span class="rounded bg-zinc-100 px-1.5 py-0.5 text-xs font-medium text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">{{ $item->variant->name }}</span>
                                    @endif
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $icfg['pill'] }}">
                                        {{ $icfg['label'] }}
                                    </span>
                                </div>
                                <div class="mt-1.5 flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-400 dark:text-zinc-500">
                                    <span>Qty: <strong class="text-zinc-600 dark:text-zinc-300">{{ $item->quantity }}</strong></span>
                                    <span>Unit: <strong class="text-zinc-600 dark:text-zinc-300">UGX {{ number_format($item->unit_price, 0) }}</strong></span>
                                    <span>Subtotal: <strong class="text-zinc-700 dark:text-zinc-200">UGX {{ number_format($item->subtotal, 0) }}</strong></span>
                                    @if($item->checked_out_at)
                                        <span>Checked out: <strong class="text-zinc-600 dark:text-zinc-300">{{ $item->checked_out_at->format('d M H:i') }}</strong></span>
                                    @endif
                                    @if($item->returned_at)
                                        <span>Returned: <strong class="text-emerald-600 dark:text-emerald-400">{{ $item->returned_at->format('d M H:i') }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            {{-- Item action buttons --}}
                            @can('bookings.edit')
                            @if(in_array($booking->status, ['confirmed', 'active']))
                                <div class="flex shrink-0 items-center gap-1.5">
                                    @if($item->status === 'pending')
                                        <button wire:click="checkOutItem({{ $item->id }})"
                                            class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2.5 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:hover:bg-amber-900/30 transition-colors"
                                            title="Mark as checked out">
                                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                            </svg>
                                            Check Out
                                        </button>
                                    @endif
                                    @if($item->status === 'checked_out')
                                        <button wire:click="markItemInCleaning({{ $item->id }})"
                                            class="inline-flex items-center gap-1 rounded-md bg-sky-50 px-2.5 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-100 dark:bg-sky-900/20 dark:text-sky-400 dark:hover:bg-sky-900/30 transition-colors"
                                            title="Send to cleaning">
                                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Cleaning
                                        </button>
                                        <button wire:click="returnItem({{ $item->id }})"
                                            class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/30 transition-colors"
                                            title="Mark as returned">
                                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"/>
                                            </svg>
                                            Return
                                        </button>
                                    @endif
                                    @if($item->status === 'in_cleaning')
                                        <button wire:click="returnItem({{ $item->id }})"
                                            class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/30 transition-colors"
                                            title="Mark as returned from cleaning">
                                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Return to Stock
                                        </button>
                                    @endif
                                    @if($item->status === 'returned')
                                        <span class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400">
                                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Back in stock
                                        </span>
                                    @endif
                                </div>
                            @endif
                            @endcan
                        </div>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-zinc-400 dark:text-zinc-500">No items in this booking.</div>
                    @endforelse
                </div>
            </div>

            {{-- Payments --}}
            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Payments</h2>
                    <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $booking->payments->count() }} recorded</span>
                </div>

                @if($booking->payments->count() > 0)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-100 bg-zinc-50 dark:border-zinc-700/50 dark:bg-zinc-800/40">
                                <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Date</th>
                                <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Method</th>
                                <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Receipt #</th>
                                <th class="px-5 py-2.5 text-center text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Type</th>
                                <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Amount</th>
                                <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                            @foreach($booking->payments as $payment)
                                <tr wire:key="payment-{{ $payment->id }}">
                                    <td class="px-5 py-3 text-zinc-700 dark:text-zinc-300">{{ $payment->paid_at->format('d M Y') }}</td>
                                    <td class="px-5 py-3 text-zinc-700 dark:text-zinc-300">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                    <td class="px-5 py-3">
                                        @if($payment->receipt_number)
                                            <code class="font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $payment->receipt_number }}</code>
                                        @else
                                            <span class="text-zinc-300 dark:text-zinc-600">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        @if($payment->is_deposit)
                                            <span class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">Deposit</span>
                                        @else
                                            <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Payment</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($payment->amount, 0) }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if($payment->receipt_number)
                                                <a href="{{ route('receipts.payment', $payment) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 rounded-md bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-colors">
                                                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    Receipt
                                                </a>
                                            @endif
                                            @if($payment->is_deposit && $payment->availableForRefund() > 0)
                                                @can('payments.create')
                                                <button wire:click="openRefundForm({{ $payment->id }})"
                                                    class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:hover:bg-amber-900/30 transition-colors">
                                                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                                    Refund
                                                </button>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-5 py-10 text-center text-sm text-zinc-400 dark:text-zinc-500">No payments recorded yet.</div>
                @endif
            </div>

        </div>

        {{-- Right Column --}}
        <div class="space-y-4">

            {{-- Financial Summary --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h3 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Financial Summary</h3>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">Total Amount</span>
                        <span class="font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($booking->total_amount, 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">Deposit Paid</span>
                        <span class="tabular-nums text-zinc-700 dark:text-zinc-300">UGX {{ number_format($this->depositPaid, 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">Total Paid</span>
                        <span class="tabular-nums font-medium text-emerald-600 dark:text-emerald-400">UGX {{ number_format($this->amountPaid, 0) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-zinc-100 pt-2.5 dark:border-zinc-700/60">
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">Balance Due</span>
                        <span class="text-lg font-bold tabular-nums {{ $this->balanceDue > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            UGX {{ number_format($this->balanceDue, 0) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Booking Details --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h3 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Details</h3>
                <dl class="space-y-2.5 text-sm">
                    <div class="flex justify-between gap-2">
                        <dt class="text-zinc-500 dark:text-zinc-400 shrink-0">Customer</dt>
                        <dd class="font-medium text-zinc-900 dark:text-zinc-100 text-right">{{ $booking->customer->name }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-zinc-500 dark:text-zinc-400 shrink-0">Phone</dt>
                        <dd class="text-zinc-700 dark:text-zinc-300">{{ $booking->customer->phone }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-zinc-500 dark:text-zinc-400 shrink-0">Hire From</dt>
                        <dd class="text-zinc-700 dark:text-zinc-300 text-right">{{ $booking->hire_from->format('d M Y, H:i') }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-zinc-500 dark:text-zinc-400 shrink-0">Hire To</dt>
                        <dd class="text-zinc-700 dark:text-zinc-300 text-right">{{ $booking->hire_to->format('d M Y, H:i') }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-zinc-500 dark:text-zinc-400 shrink-0">Duration</dt>
                        <dd class="text-zinc-700 dark:text-zinc-300">
                            @php
                                $diff = $booking->hire_from->diff($booking->hire_to);
                                $parts = [];
                                if ($diff->days > 0) $parts[] = $diff->days . ' ' . Str::plural('day', $diff->days);
                                if ($diff->h > 0) $parts[] = $diff->h . ' ' . Str::plural('hr', $diff->h);
                                if ($diff->i > 0) $parts[] = $diff->i . ' min';
                            @endphp
                            {{ implode(' ', $parts) ?: '—' }}
                        </dd>
                    </div>
                    @if($booking->notes)
                        <div class="border-t border-zinc-100 pt-2.5 dark:border-zinc-700/60">
                            <dt class="mb-1 text-xs font-medium text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Notes</dt>
                            <dd class="text-zinc-700 dark:text-zinc-300">{{ $booking->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Item Status Legend --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h3 class="mb-3 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Item Lifecycle</h3>
                <div class="space-y-2">
                    @foreach([
                        ['status' => 'pending', 'desc' => 'Not yet collected'],
                        ['status' => 'checked_out', 'desc' => 'With the customer'],
                        ['status' => 'in_cleaning', 'desc' => 'Being cleaned/prepped'],
                        ['status' => 'returned', 'desc' => 'Back in stock'],
                    ] as $step)
                        @php $sc = $itemStatusConfig[$step['status']]; @endphp
                        <div class="flex items-center gap-2.5">
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $sc['pill'] }} shrink-0">{{ $sc['label'] }}</span>
                            <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $step['desc'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    {{-- Deposit Refunds --}}
    @if($booking->depositRefunds->count() > 0)
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-700/60">
                <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Deposit Refunds</h2>
                <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $booking->depositRefunds->count() }} recorded</span>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-100 bg-zinc-50 dark:border-zinc-700/50 dark:bg-zinc-800/40">
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Date</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Ref #</th>
                        <th class="px-5 py-2.5 text-left text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Reason</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Amount</th>
                        <th class="px-5 py-2.5 text-right text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                    @foreach($booking->depositRefunds as $refund)
                        <tr wire:key="refund-{{ $refund->id }}">
                            <td class="px-5 py-3 text-zinc-700 dark:text-zinc-300">{{ $refund->refunded_at->format('d M Y') }}</td>
                            <td class="px-5 py-3"><code class="font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $refund->refund_number }}</code></td>
                            <td class="px-5 py-3 text-zinc-500 dark:text-zinc-400">{{ $refund->reason ?: '—' }}</td>
                            <td class="px-5 py-3 text-right font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($refund->amount, 0) }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('receipts.refund', $refund) }}" target="_blank"
                                    class="inline-flex items-center gap-1 rounded-md bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-colors">
                                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Receipt
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Record Payment Modal --}}
    <flux:modal name="record-payment" class="md:w-[30rem]">
        <form wire:submit="recordPayment" class="space-y-5">
            <div class="border-b border-zinc-100 pb-4 dark:border-zinc-700">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Record Payment</h3>
                <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                    Balance due: <strong class="text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->balanceDue, 0) }}</strong>
                </p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Amount <span class="text-red-500">*</span></label>
                    <flux:input wire:model="paymentAmount" type="number" step="0.01" min="0.01" placeholder="0.00" />
                    <flux:error name="paymentAmount" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Method <span class="text-red-500">*</span></label>
                        <select wire:model="paymentMethod"
                            class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                        <flux:error name="paymentMethod" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Date <span class="text-red-500">*</span></label>
                        <flux:input wire:model="paymentPaidAt" type="date" />
                        <flux:error name="paymentPaidAt" />
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Reference</label>
                    <flux:input wire:model="paymentReference" placeholder="Transaction ID or receipt number" />
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Notes</label>
                    <flux:textarea wire:model="paymentNotes" rows="2" placeholder="Optional notes…" />
                </div>

                <label class="flex items-center gap-2.5 cursor-pointer">
                    <flux:checkbox wire:model="paymentIsDeposit" />
                    <span class="text-sm text-zinc-700 dark:text-zinc-300">Mark as deposit</span>
                </label>
            </div>

            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('record-payment').close()" variant="ghost" type="button">Cancel</flux:button>
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-black hover:bg-amber-400 transition-colors">
                    Record Payment
                </button>
            </div>
        </form>
    </flux:modal>

    {{-- Refund Deposit Modal --}}
    <flux:modal name="refund-deposit" class="md:w-[28rem]">
        <form wire:submit="processRefund" class="space-y-5">
            <div class="border-b border-zinc-100 pb-4 dark:border-zinc-700">
                <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Refund Security Deposit</h3>
                <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Record a deposit refund to the customer.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Refund Amount (UGX) <span class="text-red-500">*</span></label>
                    <flux:input wire:model="refundAmount" type="number" step="0.01" min="0.01" placeholder="0.00" />
                    <flux:error name="refundAmount" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Date <span class="text-red-500">*</span></label>
                    <flux:input wire:model="refundDate" type="date" />
                    <flux:error name="refundDate" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Reason</label>
                    <flux:textarea wire:model="refundReason" rows="2" placeholder="e.g. Items returned in good condition" />
                    <flux:error name="refundReason" />
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('refund-deposit').close()" variant="ghost" type="button">Cancel</flux:button>
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-black hover:bg-amber-400 transition-colors">
                    Process Refund
                </button>
            </div>
        </form>
    </flux:modal>

</div>
