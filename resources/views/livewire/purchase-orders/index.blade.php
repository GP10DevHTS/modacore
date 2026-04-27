<div class="space-y-5">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Purchase Orders</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Manage supplier orders and receive inventory stock.</p>
        </div>
        @can('inventory.create')
        <a href="{{ route('purchase-orders.create') }}" wire:navigate
            class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-black shadow-sm hover:bg-amber-400 active:bg-amber-600 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Order
        </a>
        @endcan
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 border-b border-zinc-200 bg-zinc-50/60 px-5 py-3 dark:border-zinc-700/60 dark:bg-zinc-800/40">
            <div class="relative w-64">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search PO or supplier…"
                    class="w-full rounded-lg border border-zinc-200 bg-white py-2 pl-9 pr-3 text-sm text-zinc-900 placeholder-zinc-400 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder-zinc-500"/>
            </div>
            <div class="flex gap-1.5">
                @foreach(['', 'draft', 'sent', 'received', 'cancelled'] as $s)
                    @php
                        $labels = ['' => 'All', 'draft' => 'Draft', 'sent' => 'Sent', 'received' => 'Received', 'cancelled' => 'Cancelled'];
                        $colors = [
                            '' => 'bg-zinc-100 text-zinc-700 ring-zinc-200 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-700',
                            'draft' => 'bg-zinc-100 text-zinc-700 ring-zinc-200 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-700',
                            'sent' => 'bg-blue-50 text-blue-700 ring-blue-100 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:ring-blue-800/30',
                            'received' => 'bg-emerald-50 text-emerald-700 ring-emerald-100 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:ring-emerald-800/30',
                            'cancelled' => 'bg-red-50 text-red-700 ring-red-100 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:ring-red-800/30',
                        ];
                        $activeClass = $statusFilter === $s ? 'ring-2 font-semibold' : 'ring-1';
                    @endphp
                    <button wire:click="$set('statusFilter', '{{ $s }}')"
                        class="rounded-md px-2.5 py-1 text-xs transition-colors {{ $colors[$s] }} {{ $activeClass }}">
                        {{ $labels[$s] }}
                    </button>
                @endforeach
            </div>
            <span class="ml-auto text-xs font-medium text-zinc-400 dark:text-zinc-500">{{ $this->orders->total() }} {{ Str::plural('order', $this->orders->total()) }}</span>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">PO Number</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Supplier</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Expected</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @forelse($this->orders as $order)
                    @php
                        $statusConfig = [
                            'draft'     => ['pill' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400', 'dot' => 'bg-zinc-400'],
                            'sent'      => ['pill' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400', 'dot' => 'bg-blue-500'],
                            'received'  => ['pill' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400', 'dot' => 'bg-emerald-500'],
                            'cancelled' => ['pill' => 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400', 'dot' => 'bg-red-500'],
                        ];
                        $cfg = $statusConfig[$order->status] ?? $statusConfig['draft'];
                    @endphp
                    <tr class="group transition-colors hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30" wire:key="order-{{ $order->id }}">
                        <td class="px-5 py-3.5">
                            @can('inventory.edit')
                            <a href="{{ route('purchase-orders.edit', $order->id) }}" wire:navigate
                                class="font-mono text-sm font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300 transition-colors">
                                {{ $order->po_number }}
                            </a>
                            @else
                            <span class="font-mono text-sm font-semibold text-zinc-600 dark:text-zinc-400">{{ $order->po_number }}</span>
                            @endcan
                        </td>
                        <td class="px-5 py-3.5 font-medium text-zinc-900 dark:text-zinc-100">{{ $order->supplier->name }}</td>
                        <td class="px-5 py-3.5 text-zinc-500 dark:text-zinc-400">
                            {{ $order->expected_at ? $order->expected_at->format('d M Y') : '—' }}
                        </td>
                        <td class="px-5 py-3.5 text-right font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">
                            UGX {{ number_format($order->total_amount, 0) }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $cfg['pill'] }}">
                                <span class="size-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                @can('inventory.edit')
                                @if($order->status === 'draft')
                                    <a href="{{ route('purchase-orders.edit', $order->id) }}" wire:navigate
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors" title="Edit">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                @endif
                                @if($order->status === 'sent')
                                    <button wire:click="markReceived({{ $order->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-900/20 dark:hover:text-emerald-400 transition-colors" title="Mark Received">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                @endif
                                @if(! in_array($order->status, ['received', 'cancelled']))
                                    <button wire:click="cancelOrder({{ $order->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors" title="Cancel">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                @if($search || $statusFilter) No orders match your filters @else No purchase orders yet @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($this->orders->hasPages())
            <div class="border-t border-zinc-200 px-5 py-3 dark:border-zinc-700/60">{{ $this->orders->links() }}</div>
        @endif
    </div>

</div>
