<div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">

    <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
        <div>
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Procurement Audits</h3>
            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Recent purchase order actions and changes.</p>
        </div>
        <a href="{{ route('procurement.dashboard') }}" wire:navigate class="text-xs text-amber-600 hover:underline dark:text-amber-400">Procurement →</a>
    </div>

    @php
        $actionColors = [
            'approved'        => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400',
            'sent'            => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
            'cancelled'       => 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400',
            'goods_received'  => 'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400',
            'invoice_created' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
            'payment_recorded'=> 'bg-teal-50 text-teal-700 dark:bg-teal-900/20 dark:text-teal-400',
        ];
    @endphp

    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-zinc-100 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">PO</th>
                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Action</th>
                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Description</th>
                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">User</th>
                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">When</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
            @forelse($this->logs as $log)
                <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors" wire:key="al-{{ $log->id }}">
                    <td class="px-5 py-3">
                        @if($log->purchaseOrder)
                            <a href="{{ route('purchase-orders.show', $log->purchase_order_id) }}" wire:navigate
                                class="font-mono text-xs font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400">
                                {{ $log->purchaseOrder->po_number }}
                            </a>
                            @if($log->purchaseOrder->supplier)
                                <p class="text-xs text-zinc-400">{{ $log->purchaseOrder->supplier->name }}</p>
                            @endif
                        @else
                            <span class="font-mono text-xs text-zinc-400">#{{ $log->purchase_order_id }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $actionColors[$log->action] ?? 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' }}">
                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 max-w-xs">
                        <p class="truncate text-xs text-zinc-600 dark:text-zinc-400">{{ $log->description ?? '—' }}</p>
                    </td>
                    <td class="px-5 py-3 text-xs text-zinc-600 dark:text-zinc-400">
                        {{ $log->user?->name ?? 'System' }}
                        {{-- <br> --}}
                    </td>
                    <td class="px-5 py-3 text-right text-xs text-zinc-400 dark:text-zinc-500 whitespace-nowrap">
                        {{ $log->created_at?->diffForHumans() }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-5 py-10 text-center text-sm text-zinc-400">No audit logs recorded yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>
