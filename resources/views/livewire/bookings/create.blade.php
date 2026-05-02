<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('bookings.index') }}" wire:navigate
            class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:bg-zinc-50 hover:text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 transition-colors">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                {{ $editingBookingId ? 'Edit Booking' : 'New Booking' }}
            </h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                {{ $editingBookingId ? 'Update booking details and items.' : 'Create a new hire booking.' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Left: Form --}}
        <div class="space-y-5 lg:col-span-2">

            {{-- Booking Details --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Booking Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Customer <span class="text-red-500">*</span></label>
                        <select wire:model.live="customerId"
                            class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                            <option value="">Select a customer</option>
                            @foreach($this->customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} — {{ $customer->phone }}</option>
                            @endforeach
                        </select>
                        <flux:error name="customerId" />
                    </div>

                    {{-- Customer Measurements --}}
                    @if($customerId && $this->selectedCustomerMeasurements)
                        @php $m = $this->selectedCustomerMeasurements; @endphp
                        <div class="rounded-lg border border-zinc-100 bg-zinc-50 p-3 dark:border-zinc-700/60 dark:bg-zinc-800/40">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Body Measurements (cm)</p>
                            <div class="flex flex-wrap gap-x-4 gap-y-1.5 text-xs">
                                @foreach([
                                    'Chest' => $m->chest, 'Waist' => $m->waist, 'Hips' => $m->hips,
                                    'Shoulder' => $m->shoulder_width, 'Sleeve' => $m->sleeve_length,
                                    'Inseam' => $m->inseam, 'Neck' => $m->neck, 'Height' => $m->height,
                                ] as $label => $val)
                                    @if($val)
                                    <span class="text-zinc-500 dark:text-zinc-400">{{ $label }}: <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ number_format($val, 1) }}</span></span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @elseif($customerId)
                        <p class="text-xs text-zinc-400 dark:text-zinc-500">No measurements on file for this customer.</p>
                    @endif
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Hire From <span class="text-red-500">*</span></label>
                            <flux:input wire:model.live="hireFrom" type="datetime-local" />
                            <flux:error name="hireFrom" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Hire To <span class="text-red-500">*</span></label>
                            <flux:input wire:model.live="hireTo" type="datetime-local" />
                            <flux:error name="hireTo" />
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Notes</label>
                        <flux:textarea wire:model="notes" rows="2" placeholder="Any special instructions…" />
                    </div>
                </div>
            </div>

            {{-- Add Item --}}
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Add Item</h2>
                <div class="grid grid-cols-2 items-end gap-3">
                    <div class="min-w-0 flex-1 col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Item <span class="text-red-500">*</span></label>
                        <select wire:model.live="pickerItemId"
                            class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                            <option value="">Select item</option>
                            @foreach($this->inventoryItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} — UGX {{ number_format($item->base_rental_price, 0) }}</option>
                            @endforeach
                        </select>
                        <flux:error name="pickerItemId" />
                    </div>
                    @if($this->selectedItemVariants->count() > 0)
                        <div class="col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Composition</label>
                            <x-searchable-select
                                :options="$this->selectedItemCompositions"
                                wire-model="pickerCompositionKey"
                                :selected-value="$pickerCompositionKey"
                                placeholder="Select composition"
                                empty-message="No compositions available"
                                :clearable="false"
                            />
                        </div>
                    @endif
                    @if($this->selectedCompositionVariants->count() > 0)
                        <div class=" col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Tracked Units</label>
                            <x-searchable-select
                                :options="$this->selectedCompositionVariants"
                                wire-model="pickerVariantIds"
                                :selected-value="$pickerVariantIds"
                                placeholder="Select units"
                                empty-message="No units available"
                                multiple
                            />
                        </div>
                    @endif
                    <div class="col-span-1">
                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Unit Price <span class="text-red-500">*</span></label>
                        <flux:input wire:model="pickerUnitPrice" type="number" min="0" step="1" placeholder="0" />
                        <flux:error name="pickerUnitPrice" />
                    </div>
{{--                    <div class="w-20">Add Item--}}
{{--                        <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Qty</label>--}}
{{--                        <flux:input wire:model="pickerQuantity" type="number" min="1" />--}}
{{--                        <flux:error name="pickerQuantity" />--}}
{{--                    </div>--}}
                    <button wire:click="addLineItem"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 transition-colors shrink-0">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add
                    </button>
                </div>
            </div>

            {{-- Line Items --}}
            @if(count($lineItems) > 0)
                <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-3.5 dark:border-zinc-700/60">
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Booking Items</h2>
                        <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ count($lineItems) }} {{ Str::plural('item', count($lineItems)) }}</span>
                    </div>

                    <div class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                        @foreach ($lineItems as $index => $line)
                            <div class="flex items-center gap-4 px-5 py-3.5" wire:key="line-{{ $index }}">

                                {{-- Item info --}}
                                <div class="min-w-0 flex-1">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $line['item_name'] }}</div>
                                    @if($line['variant_name'])
                                        <div class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">{{ $line['variant_name'] }}</div>
                                    @endif
                                    @if(isset($availabilityErrors[$index]))
                                        <div class="mt-0.5 flex items-center gap-1 text-xs text-red-500">
                                            <svg class="size-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                            {{ $availabilityErrors[$index] }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Unit price (editable) --}}
                                <div class="hidden w-32 sm:block">
                                    <div class="mb-1 text-xs text-zinc-400 dark:text-zinc-500">Unit price</div>
                                    <div class="flex items-center rounded-lg border border-zinc-200 bg-zinc-50 px-2.5 py-1 dark:border-zinc-700 dark:bg-zinc-800">
                                        <span class="mr-1 text-xs text-zinc-400">UGX</span>
                                        <input
                                            type="number"
                                            min="0"
                                            step="1"
                                            value="{{ $line['unit_price'] }}"
                                            wire:change="updateLinePrice({{ $index }}, $event.target.value)"
                                            class="w-full bg-transparent text-sm font-medium tabular-nums text-zinc-800 focus:outline-none dark:text-zinc-200"
                                        />
                                    </div>
                                </div>

                                {{-- Quantity stepper --}}
                                <div class="flex items-center gap-1 rounded-lg border border-zinc-200 bg-zinc-50 p-1 dark:border-zinc-700 dark:bg-zinc-800">
                                    <button type="button"
                                        wire:click="updateLineQuantity({{ $index }}, {{ max(1, $line['quantity'] - 1) }})"
                                        class="flex size-6 items-center justify-center rounded text-zinc-500 hover:bg-white hover:text-zinc-700 dark:hover:bg-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                    </button>
                                    <span class="w-8 text-center text-sm font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">{{ $line['quantity'] }}</span>
                                    <button type="button"
                                        wire:click="updateLineQuantity({{ $index }}, {{ $line['quantity'] + 1 }})"
                                        class="flex size-6 items-center justify-center rounded text-zinc-500 hover:bg-white hover:text-zinc-700 dark:hover:bg-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                        <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>

                                {{-- Subtotal --}}
                                <div class="w-24 text-right">
                                    <div class="font-semibold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($line['subtotal'], 0) }}</div>
                                </div>

                                {{-- Remove --}}
                                <button wire:click="removeLineItem({{ $index }})"
                                    class="rounded-lg p-1.5 text-zinc-300 hover:bg-red-50 hover:text-red-500 dark:text-zinc-600 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>

                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-end gap-2 border-t border-zinc-100 bg-zinc-50/60 px-5 py-3 dark:border-zinc-700/60 dark:bg-zinc-800/40">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Total</span>
                        <span class="text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->totalAmount, 0) }}</span>
                    </div>
                </div>
            @endif

        </div>

        {{-- Right: Summary & Actions --}}
        <div class="space-y-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-zinc-100">Summary</h2>

                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between text-zinc-500 dark:text-zinc-400">
                        <span>Items added</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ count($lineItems) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-zinc-100 pt-2.5 dark:border-zinc-700/60">
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">Total</span>
                        <span class="text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->totalAmount, 0) }}</span>
                    </div>
                </div>

                <div class="mt-5 space-y-2.5">
                    <button wire:click="save('confirmed')"
                        @if(count($lineItems) === 0) disabled @endif
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-black hover:bg-amber-400 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $editingBookingId ? 'Save & Confirm' : 'Create & Confirm' }}
                    </button>
                    <button wire:click="save('draft')"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-colors">
                        Save as Draft
                    </button>
                </div>
            </div>

            @if(count($lineItems) === 0)
                <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 p-5 text-center dark:border-zinc-700 dark:bg-zinc-800/40">
                    <svg class="mx-auto mb-2 size-8 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">No items added yet. Use the form above to add items.</p>
                </div>
            @endif
        </div>

    </div>

</div>
