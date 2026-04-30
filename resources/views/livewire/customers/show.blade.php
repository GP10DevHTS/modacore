<div class="space-y-6">

    @php
        $statusConfig = [
            'draft'     => ['pill' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400', 'label' => 'Draft'],
            'confirmed' => ['pill' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400', 'label' => 'Confirmed'],
            'active'    => ['pill' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400', 'label' => 'Active'],
            'completed' => ['pill' => 'bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-400', 'label' => 'Completed'],
            'cancelled' => ['pill' => 'bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400', 'label' => 'Cancelled'],
        ];
    @endphp

    {{-- Page Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('customers.index') }}" wire:navigate
                class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:bg-zinc-50 hover:text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition-colors">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex items-center gap-3">
                <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-amber-100 text-base font-bold text-amber-700 dark:bg-amber-900/40 dark:text-amber-400">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $customer->name }}</h1>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ $customer->phone }}@if($customer->email) &middot; {{ $customer->email }}@endif</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Left column --}}
        <div class="space-y-5 lg:col-span-2">

            {{-- Body Measurements --}}
            <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-4 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Body Measurements <span class="ml-1.5 text-xs font-normal text-zinc-400">(cm)</span></h2>
                </div>

                <form wire:submit="saveMeasurements" class="p-5">
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-zinc-600 dark:text-zinc-400">Chest</label>
                            <flux:input wire:model="chest" type="number" step="0.1" min="0" placeholder="—" />
                            <flux:error name="chest" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-zinc-600 dark:text-zinc-400">Waist</label>
                            <flux:input wire:model="waist" type="number" step="0.1" min="0" placeholder="—" />
                            <flux:error name="waist" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-zinc-600 dark:text-zinc-400">Hips</label>
                            <flux:input wire:model="hips" type="number" step="0.1" min="0" placeholder="—" />
                            <flux:error name="hips" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-zinc-600 dark:text-zinc-400">Shoulder Width</label>
                            <flux:input wire:model="shoulderWidth" type="number" step="0.1" min="0" placeholder="—" />
                            <flux:error name="shoulderWidth" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-zinc-600 dark:text-zinc-400">Sleeve Length</label>
                            <flux:input wire:model="sleeveLength" type="number" step="0.1" min="0" placeholder="—" />
                            <flux:error name="sleeveLength" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-zinc-600 dark:text-zinc-400">Inseam</label>
                            <flux:input wire:model="inseam" type="number" step="0.1" min="0" placeholder="—" />
                            <flux:error name="inseam" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-zinc-600 dark:text-zinc-400">Neck</label>
                            <flux:input wire:model="neck" type="number" step="0.1" min="0" placeholder="—" />
                            <flux:error name="neck" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-zinc-600 dark:text-zinc-400">Height</label>
                            <flux:input wire:model="height" type="number" step="0.1" min="0" placeholder="—" />
                            <flux:error name="height" />
                        </div>
                    </div>

                    @can('customers.edit')
                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-black hover:bg-amber-400 active:bg-amber-600 transition-colors">
                            Save Measurements
                        </button>
                    </div>
                    @endcan
                </form>
            </div>

            {{-- Bookings History --}}
            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-4 dark:border-zinc-700/60">
                    <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Booking History</h2>
                    <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $this->bookings->count() }} {{ Str::plural('booking', $this->bookings->count()) }}</span>
                </div>

                @if($this->bookings->isEmpty())
                    <div class="px-5 py-10 text-center">
                        <svg class="mx-auto mb-3 size-8 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm text-zinc-400 dark:text-zinc-500">No bookings yet.</p>
                    </div>
                @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-100 bg-zinc-50/60 dark:border-zinc-700/60 dark:bg-zinc-800/40">
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Booking #</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Period</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Amount</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                            @foreach($this->bookings as $booking)
                                @php $cfg = $statusConfig[$booking->status] ?? $statusConfig['draft']; @endphp
                                <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors" wire:key="booking-{{ $booking->id }}">
                                    <td class="px-5 py-3.5">
                                        <span class="font-mono text-xs font-semibold text-zinc-700 dark:text-zinc-300">{{ $booking->booking_number }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-zinc-600 dark:text-zinc-400">
                                        <div class="text-xs">{{ $booking->hire_from->format('d M Y') }}</div>
                                        <div class="text-xs text-zinc-400">→ {{ $booking->hire_to->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $cfg['pill'] }}">
                                            {{ $cfg['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">
                                        UGX {{ number_format($booking->total_amount, 0) }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <a href="{{ route('bookings.show', $booking->id) }}" wire:navigate
                                            class="text-xs font-medium text-amber-600 hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300">
                                            View →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>

        {{-- Right column: Profile Details --}}
        <div class="space-y-5">

            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Profile</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium text-zinc-400 dark:text-zinc-500">Phone</dt>
                        <dd class="mt-0.5 text-zinc-800 dark:text-zinc-200">{{ $customer->phone }}</dd>
                    </div>
                    @if($customer->email)
                    <div>
                        <dt class="text-xs font-medium text-zinc-400 dark:text-zinc-500">Email</dt>
                        <dd class="mt-0.5 text-zinc-800 dark:text-zinc-200">{{ $customer->email }}</dd>
                    </div>
                    @endif
                    @if($customer->id_number)
                    <div>
                        <dt class="text-xs font-medium text-zinc-400 dark:text-zinc-500">ID / Passport</dt>
                        <dd class="mt-0.5">
                            <code class="rounded bg-zinc-100 px-2 py-0.5 font-mono text-xs text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $customer->id_number }}</code>
                        </dd>
                    </div>
                    @endif
                    @if($customer->address)
                    <div>
                        <dt class="text-xs font-medium text-zinc-400 dark:text-zinc-500">Address</dt>
                        <dd class="mt-0.5 text-zinc-800 dark:text-zinc-200">{{ $customer->address }}</dd>
                    </div>
                    @endif
                    @if($customer->notes)
                    <div>
                        <dt class="text-xs font-medium text-zinc-400 dark:text-zinc-500">Notes</dt>
                        <dd class="mt-0.5 text-zinc-600 dark:text-zinc-400">{{ $customer->notes }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-zinc-400 dark:text-zinc-500">Member Since</dt>
                        <dd class="mt-0.5 text-zinc-800 dark:text-zinc-200">{{ $customer->created_at->format('d M Y') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Measurements Summary --}}
            @if($customer->measurements && collect([$customer->measurements->chest, $customer->measurements->waist, $customer->measurements->hips, $customer->measurements->height])->filter()->isNotEmpty())
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-3 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Measurements Summary</h2>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    @foreach([
                        'Chest' => $customer->measurements->chest,
                        'Waist' => $customer->measurements->waist,
                        'Hips' => $customer->measurements->hips,
                        'Height' => $customer->measurements->height,
                        'Shoulder' => $customer->measurements->shoulder_width,
                        'Sleeve' => $customer->measurements->sleeve_length,
                        'Inseam' => $customer->measurements->inseam,
                        'Neck' => $customer->measurements->neck,
                    ] as $label => $val)
                        @if($val)
                        <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-2.5 py-1.5 dark:bg-zinc-800/40">
                            <span class="text-zinc-500 dark:text-zinc-400">{{ $label }}</span>
                            <span class="font-semibold tabular-nums text-zinc-800 dark:text-zinc-200">{{ number_format($val, 1) }} cm</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

        </div>

    </div>

</div>
