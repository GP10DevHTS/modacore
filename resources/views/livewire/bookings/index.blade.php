<div class="space-y-5">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Bookings</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Track and manage all hire bookings.</p>
        </div>
        @can('bookings.create')
        <a href="{{ route('bookings.create') }}" wire:navigate
            class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-black shadow-sm hover:bg-amber-400 active:bg-amber-600 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Booking
        </a>
        @endcan
    </div>

    {{-- Table Card --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center gap-3 border-b border-zinc-200 bg-zinc-50/60 px-5 py-3 dark:border-zinc-700/60 dark:bg-zinc-800/40">
            <div class="relative w-64">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search reference or customer…"
                    class="w-full rounded-lg border border-zinc-200 bg-white py-2 pl-9 pr-3 text-sm text-zinc-900 placeholder-zinc-400 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:placeholder-zinc-500"
                />
            </div>
            <div class="flex gap-1.5">
                @foreach(['', 'draft', 'confirmed', 'active', 'completed', 'cancelled'] as $s)
                    @php
                        $labels = ['' => 'All', 'draft' => 'Draft', 'confirmed' => 'Confirmed', 'active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
                        $colors = [
                            '' => 'bg-zinc-100 text-zinc-700 ring-zinc-200 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-700',
                            'draft' => 'bg-zinc-100 text-zinc-700 ring-zinc-200 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:ring-zinc-700',
                            'confirmed' => 'bg-blue-50 text-blue-700 ring-blue-100 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:ring-blue-800/30',
                            'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-100 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:ring-emerald-800/30',
                            'completed' => 'bg-violet-50 text-violet-700 ring-violet-100 hover:bg-violet-100 dark:bg-violet-900/20 dark:text-violet-400 dark:ring-violet-800/30',
                            'cancelled' => 'bg-red-50 text-red-700 ring-red-100 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:ring-red-800/30',
                        ];
                        $activeClass = $statusFilter === $s
                            ? 'ring-2 font-semibold'
                            : 'ring-1';
                    @endphp
                    <button wire:click="$set('statusFilter', '{{ $s }}')"
                        class="rounded-md px-2.5 py-1 text-xs transition-colors {{ $colors[$s] }} {{ $activeClass }}">
                        {{ $labels[$s] }}
                    </button>
                @endforeach
            </div>
            <span class="ml-auto text-xs font-medium text-zinc-400 dark:text-zinc-500">
                {{ $this->bookings->total() }} {{ Str::plural('booking', $this->bookings->total()) }}
            </span>
        </div>

        {{-- Table --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Reference</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Customer</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Hire Period</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Total</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @forelse ($this->bookings as $booking)
                    @php
                        $statusConfig = [
                            'draft'     => ['pill' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400', 'dot' => 'bg-zinc-400'],
                            'confirmed' => ['pill' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400', 'dot' => 'bg-blue-500'],
                            'active'    => ['pill' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400', 'dot' => 'bg-emerald-500'],
                            'completed' => ['pill' => 'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400', 'dot' => 'bg-violet-500'],
                            'cancelled' => ['pill' => 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400', 'dot' => 'bg-red-500'],
                        ];
                        $cfg = $statusConfig[$booking->status] ?? $statusConfig['draft'];
                    @endphp
                    <tr class="group transition-colors hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30" wire:key="booking-{{ $booking->id }}">

                        <td class="px-5 py-3.5">
                            <a href="{{ route('bookings.show', $booking->id) }}" wire:navigate
                                class="font-mono text-sm font-semibold text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300 transition-colors">
                                {{ $booking->booking_number }}
                            </a>
                        </td>

                        <td class="px-5 py-3.5">
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $booking->customer->name }}</div>
                            <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">{{ $booking->customer->phone }}</div>
                        </td>

                        <td class="px-5 py-3.5">
                            <div class="text-zinc-800 dark:text-zinc-200">{{ $booking->hire_from->format('d M Y, H:i') }}</div>
                            <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">→ {{ $booking->hire_to->format('d M Y, H:i') }}</div>
                        </td>

                        <td class="px-5 py-3.5 text-right font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">
                            UGX {{ number_format($booking->total_amount, 0) }}
                        </td>

                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $cfg['pill'] }}">
                                <span class="size-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>

                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('bookings.show', $booking->id) }}" wire:navigate
                                    class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors"
                                    title="View">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @can('bookings.edit')
                                @if($booking->status === 'draft')
                                    <button wire:click="openConfirm({{ $booking->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-blue-50 hover:text-blue-600 dark:hover:bg-blue-900/20 dark:hover:text-blue-400 transition-colors"
                                        title="Confirm">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                @endif
                                @if($booking->status === 'confirmed')
                                    <button wire:click="markActive({{ $booking->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-900/20 dark:hover:text-emerald-400 transition-colors"
                                        title="Mark Active">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                @endif
                                @if($booking->status === 'active')
                                    <button wire:click="markCompleted({{ $booking->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-violet-50 hover:text-violet-600 dark:hover:bg-violet-900/20 dark:hover:text-violet-400 transition-colors"
                                        title="Mark Completed">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                @endif
                                @if($booking->status !== 'cancelled' && $booking->status !== 'completed')
                                    <button wire:click="openCancel({{ $booking->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors"
                                        title="Cancel">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                @if($search || $statusFilter) No bookings match your filters @else No bookings yet @endif
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($this->bookings->hasPages())
            <div class="border-t border-zinc-200 px-5 py-3 dark:border-zinc-700/60">
                {{ $this->bookings->links() }}
            </div>
        @endif

    </div>

    {{-- Confirm Booking Modal --}}
    <flux:modal name="confirm-booking" class="md:w-96">
        <div class="space-y-5">
            <div class="flex items-start gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                    <svg class="size-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Confirm Booking</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Items will be reserved for the hire period. This cannot be easily undone.</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('confirm-booking').close()" variant="ghost" type="button">Back</flux:button>
                <button wire:click="confirmBooking"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-500 transition-colors">
                    Confirm Booking
                </button>
            </div>
        </div>
    </flux:modal>

    {{-- Cancel Booking Modal --}}
    <flux:modal name="cancel-booking" class="md:w-96">
        <div class="space-y-5">
            <div class="flex items-start gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                    <svg class="size-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Cancel Booking</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">This will release all reserved items. Action cannot be undone.</p>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-zinc-100 pt-4 dark:border-zinc-700">
                <flux:button x-on:click="$flux.modal('cancel-booking').close()" variant="ghost" type="button">Back</flux:button>
                <button wire:click="cancelBooking"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500 transition-colors">
                    Cancel Booking
                </button>
            </div>
        </div>
    </flux:modal>

</div>
