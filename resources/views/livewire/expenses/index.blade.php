<div class="space-y-5">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Expenses</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Track operational bills, expense items, and categories.</p>
        </div>
        @if($activeTab === 'bills')
            @can('expenses.create')
                <button wire:click="openCreate"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#3d7a69] px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#2d5c4d] transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Record Bill
                </button>
            @endcan
        @elseif($activeTab === 'items')
            @can('expenses.create')
                <button wire:click="openCreateItem"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#3d7a69] px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#2d5c4d] transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </button>
            @endcan
        @elseif($activeTab === 'categories')
            @can('expenses.create')
                <button wire:click="openCreateCategory"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#3d7a69] px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#2d5c4d] transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add Category
                </button>
            @endcan
        @endif
    </div>

    {{-- ─── Tab Navigation ─── --}}
    <div class="border-b border-zinc-200 dark:border-zinc-700">
        <nav class="flex gap-1">
            <button wire:click="setActiveTab('bills')"
                class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
                    {{ $activeTab === 'bills'
                        ? 'border-[#3d7a69] text-[#3d7a69] dark:border-[#a8c2b8] dark:text-[#a8c2b8]'
                        : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
                <div class="flex items-center gap-2">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Bills
                    @if($this->summaryStats['unpaid_count'] > 0)
                        <span class="inline-flex items-center justify-center rounded-full bg-red-100 px-1.5 py-px text-xs font-bold text-red-600 dark:bg-red-900/30 dark:text-red-400">
                            {{ $this->summaryStats['unpaid_count'] }}
                        </span>
                    @endif
                </div>
            </button>
            <button wire:click="setActiveTab('items')"
                class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
                    {{ $activeTab === 'items'
                        ? 'border-[#3d7a69] text-[#3d7a69] dark:border-[#a8c2b8] dark:text-[#a8c2b8]'
                        : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
                <div class="flex items-center gap-2">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Expense Items
                </div>
            </button>
            <button wire:click="setActiveTab('categories')"
                class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
                    {{ $activeTab === 'categories'
                        ? 'border-[#3d7a69] text-[#3d7a69] dark:border-[#a8c2b8] dark:text-[#a8c2b8]'
                        : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-200' }}">
                <div class="flex items-center gap-2">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Expense Categories
                </div>
            </button>
        </nav>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- BILLS TAB                                                               --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'bills')

        {{-- Financial Overview --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Billed</p>
                <p class="mt-1 text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->summaryStats['total_billed'], 0) }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm dark:border-emerald-800/30 dark:bg-emerald-900/10">
                <p class="text-xs font-medium text-emerald-700 dark:text-emerald-400">Total Paid</p>
                <p class="mt-1 text-lg font-bold tabular-nums text-emerald-700 dark:text-emerald-300">UGX {{ number_format($this->summaryStats['total_paid'], 0) }}</p>
            </div>
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 shadow-sm dark:border-red-800/30 dark:bg-red-900/10">
                <p class="text-xs font-medium text-red-700 dark:text-red-400">Outstanding</p>
                <p class="mt-1 text-lg font-bold tabular-nums text-red-700 dark:text-red-300">UGX {{ number_format($this->summaryStats['balance'], 0) }}</p>
            </div>
            <div class="rounded-xl border border-[#a8c2b8]/50 bg-[#a8c2b8]/10 p-4 shadow-sm dark:border-[#a8c2b8]/20 dark:bg-[#a8c2b8]/5">
                <p class="text-xs font-medium text-[#2d5c4d] dark:text-[#a8c2b8]">Unpaid Bills</p>
                <p class="mt-1 text-lg font-bold tabular-nums text-[#2d5c4d] dark:text-[#a8c2b8]">{{ $this->summaryStats['unpaid_count'] + $this->summaryStats['partial_count'] }}</p>
                <p class="mt-0.5 text-xs text-[#3d7a69]/70 dark:text-[#7fa99b]">{{ $this->summaryStats['partial_count'] }} partial</p>
            </div>
        </div>

        {{-- Status Filter Pills + Search --}}
        <div class="flex flex-wrap items-center gap-2">
            @foreach([
                '' => ['All Bills', 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-200'],
                'unpaid' => ['Unpaid', 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
                'partially_paid' => ['Partially Paid', 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'],
                'fully_paid' => ['Fully Paid', 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'],
            ] as $val => [$label, $classes])
                <button wire:click="$set('statusFilter', '{{ $val }}')"
                    class="rounded-full px-3 py-1 text-xs font-semibold transition-all
                        {{ $statusFilter === $val ? $classes.' ring-2 ring-offset-1 ring-[#3d7a69] dark:ring-[#a8c2b8]' : 'bg-zinc-50 text-zinc-500 hover:bg-zinc-100 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                    {{ $label }}
                </button>
            @endforeach

            <div class="ml-auto flex flex-wrap items-center gap-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search bills…"
                        class="w-44 rounded-lg border border-zinc-200 bg-white py-1.5 pl-8 pr-3 text-sm text-zinc-900 placeholder-zinc-400 shadow-sm focus:border-[#3d7a69] focus:outline-none focus:ring-2 focus:ring-[#3d7a69]/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />
                </div>
                <x-searchable-select
                    :options="$this->allCategories"
                    wire-model="categoryFilter"
                    :selected-value="$categoryFilter"
                    placeholder="All Categories"
                    class="w-44"
                />
                <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $this->bills->total() }} {{ Str::plural('bill', $this->bills->total()) }}</span>
            </div>
        </div>

        {{-- Bills Table --}}
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-100 bg-zinc-50/80 dark:border-zinc-700/60 dark:bg-zinc-800/50">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Bill / Item</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Category</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Date</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Billed</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Paid</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Balance</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                    @forelse($this->bills as $bill)
                        @php
                            $paid = (float) $bill->payments->sum('amount');
                            $balance = max(0, (float) $bill->amount - $paid);
                        @endphp
                        <tr class="group hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors" wire:key="bill-{{ $bill->id }}">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('expenses.show', $bill->id) }}" wire:navigate class="hover:underline">
                                    <span class="font-mono text-xs font-semibold text-[#2d4f43] dark:text-[#a8c2b8]">{{ $bill->expense_number }}</span>
                                    <p class="mt-0.5 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $bill->title }}</p>
                                </a>
                                @if($bill->item)
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $bill->item->name }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if($bill->item?->category)
                                    <span class="inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                        {{ $bill->item->category->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-sm whitespace-nowrap text-zinc-600 dark:text-zinc-400">
                                {{ $bill->expense_date->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold {{ $bill->payment_status->pillClasses() }}">
                                    <span class="size-1.5 rounded-full {{ $bill->payment_status->dotClasses() }}"></span>
                                    {{ $bill->payment_status->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-semibold text-zinc-900 dark:text-zinc-100 whitespace-nowrap">
                                UGX {{ number_format($bill->amount, 0) }}
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums text-emerald-700 dark:text-emerald-400 whitespace-nowrap">
                                @if($paid > 0) UGX {{ number_format($paid, 0) }} @else <span class="text-zinc-300 dark:text-zinc-600">—</span> @endif
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums whitespace-nowrap {{ $balance > 0 ? 'font-semibold text-red-600 dark:text-red-400' : 'text-zinc-400 dark:text-zinc-500' }}">
                                @if($balance > 0) UGX {{ number_format($balance, 0) }} @else <span class="text-emerald-500">✓</span> @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('expenses.show', $bill->id) }}" wire:navigate
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors"
                                        title="View details">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @can('expenses.edit')
                                        <button wire:click="openEdit({{ $bill->id }})"
                                            class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors"
                                            title="Edit">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    @endcan
                                    @can('expenses.delete')
                                        <button wire:click="deleteBill({{ $bill->id }})"
                                            wire:confirm="Delete this bill and all its payment records?"
                                            class="rounded-lg p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors"
                                            title="Delete">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm font-medium text-zinc-400 dark:text-zinc-500">No bills recorded yet.</p>
                                @can('expenses.create')
                                    <button wire:click="openCreate" class="mt-2 text-xs text-[#2d4f43] hover:underline dark:text-[#a8c2b8]">Record your first bill</button>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($this->bills->hasPages())
            <div>{{ $this->bills->links() }}</div>
        @endif

    @endif
    {{-- end bills tab --}}

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- EXPENSE ITEMS TAB                                                       --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'items')

        {{-- Search + category filter --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="searchItems" type="text" placeholder="Search items…"
                    class="w-52 rounded-lg border border-zinc-200 bg-white py-2 pl-8 pr-3 text-sm text-zinc-900 placeholder-zinc-400 shadow-sm focus:border-[#3d7a69] focus:outline-none focus:ring-2 focus:ring-[#3d7a69]/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />
            </div>
            <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $this->items->count() }} {{ Str::plural('item', $this->items->count()) }}</span>
        </div>

        {{-- Category filter chips --}}
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="$set('selectedCategoryId', null)"
                class="rounded-full px-3 py-1 text-xs font-medium transition-colors
                    {{ is_null($selectedCategoryId) ? 'bg-[#a8c2b8] text-[#1a3028]' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-700 dark:text-zinc-300' }}">
                All Categories
            </button>
            @foreach($this->allCategories as $cat)
                <button wire:click="$set('selectedCategoryId', {{ $cat->id }})"
                    class="rounded-full px-3 py-1 text-xs font-medium transition-colors
                        {{ $selectedCategoryId == $cat->id ? 'bg-[#a8c2b8] text-[#1a3028]' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-700 dark:text-zinc-300' }}">
                    {{ $cat->name }}
                    <span class="ml-1 opacity-60">{{ $cat->items_count }}</span>
                </button>
            @endforeach
        </div>

        {{-- Items list --}}
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-100 bg-zinc-50/80 dark:border-zinc-700/60 dark:bg-zinc-800/50">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Item Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Category</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Description</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Bills</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                    @forelse($this->items as $item)
                        <tr class="group hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors" wire:key="item-{{ $item->id }}">
                            <td class="px-5 py-3.5 font-medium text-zinc-900 dark:text-zinc-100">{{ $item->name }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ $item->category->name }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $item->description ?: '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums text-zinc-600 dark:text-zinc-400">
                                {{ $item->expenses_count }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @can('expenses.create')
                                        <button wire:click="openEditItem({{ $item->id }})"
                                            class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors" title="Edit">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    @endcan
                                    @can('expenses.delete')
                                        <button wire:click="deleteItem({{ $item->id }})"
                                            wire:confirm="Delete this item? This cannot be done if it has bills recorded."
                                            class="rounded-lg p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors" title="Delete">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                <p class="text-sm font-medium text-zinc-400 dark:text-zinc-500">
                                    {{ $searchItems ? 'No items match your search.' : 'No expense items yet.' }}
                                </p>
                                @can('expenses.create')
                                    <button wire:click="openCreateItem" class="mt-2 text-xs text-[#2d4f43] hover:underline dark:text-[#a8c2b8]">Add your first item</button>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @endif
    {{-- end items tab --}}

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- EXPENSE CATEGORIES TAB                                                  --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'categories')

        {{-- Search --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="searchCategories" type="text" placeholder="Search categories…"
                    class="w-52 rounded-lg border border-zinc-200 bg-white py-2 pl-8 pr-3 text-sm text-zinc-900 placeholder-zinc-400 shadow-sm focus:border-[#3d7a69] focus:outline-none focus:ring-2 focus:ring-[#3d7a69]/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />
            </div>
            <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $this->categories->count() }} {{ Str::plural('category', $this->categories->count()) }}</span>
        </div>

        {{-- Categories list --}}
        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-100 bg-zinc-50/80 dark:border-zinc-700/60 dark:bg-zinc-800/50">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Category</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Description</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Items</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                    @forelse($this->categories as $cat)
                        <tr class="group hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors" wire:key="cat-{{ $cat->id }}">
                            <td class="px-5 py-3.5 font-medium text-zinc-900 dark:text-zinc-100">{{ $cat->name }}</td>
                            <td class="px-5 py-3.5 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ $cat->description ?: '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums">
                                <button wire:click="setActiveTab('items'); $set('selectedCategoryId', {{ $cat->id }})"
                                    class="text-xs text-[#2d4f43] hover:underline dark:text-[#a8c2b8]">
                                    {{ $cat->items_count }} {{ Str::plural('item', $cat->items_count) }} →
                                </button>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @can('expenses.create')
                                        <button wire:click="openEditCategory({{ $cat->id }})"
                                            class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors" title="Edit">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                    @endcan
                                    @can('expenses.delete')
                                        <button wire:click="deleteCategory({{ $cat->id }})"
                                            wire:confirm="Delete this category? All its items must be removed first."
                                            class="rounded-lg p-1.5 text-zinc-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-colors" title="Delete">
                                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-16 text-center">
                                <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <p class="text-sm font-medium text-zinc-400 dark:text-zinc-500">
                                    {{ $searchCategories ? 'No categories match your search.' : 'No expense categories yet.' }}
                                </p>
                                @can('expenses.create')
                                    <button wire:click="openCreateCategory" class="mt-2 text-xs text-[#2d4f43] hover:underline dark:text-[#a8c2b8]">Add your first category</button>
                                @endcan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @endif
    {{-- end categories tab --}}

    {{-- ─── Bill Form Modal ─── --}}
    <flux:modal name="bill-form" class="md:w-[34rem]">
        <flux:heading size="lg">{{ $editingId ? 'Edit Bill' : 'Record Bill' }}</flux:heading>
        <flux:subheading>{{ $editingId ? 'Update the bill details.' : 'Record a new expense obligation.' }}</flux:subheading>

        <div class="mt-5 space-y-4">
            <flux:field>
                <flux:label>Title <span class="text-red-500">*</span></flux:label>
                <flux:input wire:model="billTitle" placeholder="e.g. April electricity bill" />
                <flux:error name="billTitle" />
            </flux:field>

            <div class="grid grid-cols-1 gap-4">
                <flux:field>
                    <flux:label>Expense Item <span class="text-red-500">*</span></flux:label>
                    <x-searchable-select
                        :options="$this->allItems"
                        wire-model="billItemId"
                        :selected-value="$billItemId"
                        placeholder="Select item…"
                    />
                    @error('billItemId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </flux:field>
                <flux:field>
                    <flux:label>Amount (UGX) <span class="text-red-500">*</span></flux:label>
                    <flux:input wire:model="billAmount" type="number" step="1" min="0" placeholder="0" />
                    <flux:error name="billAmount" />
                </flux:field>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Date <span class="text-red-500">*</span></flux:label>
                    <flux:input wire:model="billDate" type="date" />
                    <flux:error name="billDate" />
                </flux:field>
                <flux:field>
                    <flux:label>Reference</flux:label>
                    <flux:input wire:model="billReference" placeholder="Invoice / PO ref (optional)" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>Notes</flux:label>
                <flux:textarea wire:model="billNotes" rows="2" placeholder="Optional notes…" />
            </flux:field>

            @if(!$editingId)
                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/40">
                    <label class="flex cursor-pointer items-center gap-3">
                        <input wire:model.live="hasInitialPayment" type="checkbox"
                            class="size-4 rounded border-zinc-300 text-[#3d7a69] focus:ring-[#3d7a69]" />
                        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Was a payment made at the time of this bill?</span>
                    </label>

                    @if($hasInitialPayment)
                        <div class="mt-4 space-y-3 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Payment Details</p>
                            <div class="grid grid-cols-2 gap-3">
                                <flux:field>
                                    <flux:label>Amount Paid (UGX) <span class="text-red-500">*</span></flux:label>
                                    <flux:input wire:model="payAmount" type="number" step="1" min="0" placeholder="0" />
                                    <flux:error name="payAmount" />
                                </flux:field>
                                <flux:field>
                                    <flux:label>Payment Date <span class="text-red-500">*</span></flux:label>
                                    <flux:input wire:model="payDate" type="date" />
                                    <flux:error name="payDate" />
                                </flux:field>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <flux:field>
                                    <flux:label>Payment Method <span class="text-red-500">*</span></flux:label>
                                    <flux:select wire:model="payMethod">
                                        <flux:select.option value="cash">Cash</flux:select.option>
                                        <flux:select.option value="card">Card</flux:select.option>
                                        <flux:select.option value="mobile_money">Mobile Money</flux:select.option>
                                    </flux:select>
                                    <flux:error name="payMethod" />
                                </flux:field>
                                <flux:field>
                                    <flux:label>Reference</flux:label>
                                    <flux:input wire:model="payReference" placeholder="Optional" />
                                </flux:field>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <flux:button variant="ghost" x-on:click="$flux.modal('bill-form').close()">Cancel</flux:button>
            <flux:button wire:click="saveBill" wire:loading.attr="disabled" variant="primary">
                <span wire:loading.remove wire:target="saveBill">{{ $editingId ? 'Update Bill' : 'Save Bill' }}</span>
                <span wire:loading wire:target="saveBill">Saving…</span>
            </flux:button>
        </div>
    </flux:modal>

    {{-- ─── Category Form Modal ─── --}}
    <flux:modal name="category-form" class="md:w-[28rem]">
        <flux:heading size="lg">{{ $editingCategoryId ? 'Edit Category' : 'New Category' }}</flux:heading>
        <flux:subheading>{{ $editingCategoryId ? 'Update the category details.' : 'Add a new expense category.' }}</flux:subheading>

        <div class="mt-5 space-y-4">
            <flux:field>
                <flux:label>Name <span class="text-red-500">*</span></flux:label>
                <flux:input wire:model="categoryName" placeholder="e.g. Utilities" />
                <flux:error name="categoryName" />
            </flux:field>
            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea wire:model="categoryDescription" rows="2" placeholder="Optional description…" />
                <flux:error name="categoryDescription" />
            </flux:field>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <flux:button variant="ghost" x-on:click="$flux.modal('category-form').close()">Cancel</flux:button>
            <flux:button wire:click="saveCategory" wire:loading.attr="disabled" variant="primary">
                <span wire:loading.remove wire:target="saveCategory">{{ $editingCategoryId ? 'Update' : 'Add Category' }}</span>
                <span wire:loading wire:target="saveCategory">Saving…</span>
            </flux:button>
        </div>
    </flux:modal>

    {{-- ─── Item Form Modal ─── --}}
    <flux:modal name="item-form" class="md:w-[28rem]">
        <flux:heading size="lg">{{ $editingItemId ? 'Edit Item' : 'New Expense Item' }}</flux:heading>
        <flux:subheading>{{ $editingItemId ? 'Update the item details.' : 'Items are tracked expense types within a category.' }}</flux:subheading>

        <div class="mt-5 space-y-4">
            <flux:field>
                <flux:label>Category <span class="text-red-500">*</span></flux:label>
                <x-searchable-select
                    :options="$this->allCategories"
                    wire-model="itemCategoryId"
                    :selected-value="$itemCategoryId"
                    placeholder="Select category…"
                />
                <flux:error name="itemCategoryId" />
            </flux:field>
            <flux:field>
                <flux:label>Item Name <span class="text-red-500">*</span></flux:label>
                <flux:input wire:model="itemName" placeholder="e.g. Internet Subscription" />
                <flux:error name="itemName" />
            </flux:field>
            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea wire:model="itemDescription" rows="2" placeholder="Optional…" />
            </flux:field>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <flux:button variant="ghost" x-on:click="$flux.modal('item-form').close()">Cancel</flux:button>
            <flux:button wire:click="saveItem" wire:loading.attr="disabled" variant="primary">
                <span wire:loading.remove wire:target="saveItem">{{ $editingItemId ? 'Update' : 'Add Item' }}</span>
                <span wire:loading wire:target="saveItem">Saving…</span>
            </flux:button>
        </div>
    </flux:modal>

</div>
