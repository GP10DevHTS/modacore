<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Expenses</h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Record and track operational expenditure.</p>
        </div>
        <div class="flex items-center gap-2">
            @can('expenses.create')
                <button wire:click="openCategories"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3.5 py-2 text-sm font-medium text-zinc-700 shadow-sm hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Categories
                </button>
                <button wire:click="openCreate"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-black shadow-sm hover:bg-amber-400 transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Record Expense
                </button>
            @endcan
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-red-200 bg-red-50 p-5 shadow-sm dark:border-red-800/30 dark:bg-red-900/10">
            <div class="flex size-8 items-center justify-center rounded-lg bg-red-100 dark:bg-red-800/30">
                <svg class="size-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-red-700 dark:text-red-400">Total Approved</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-red-700 dark:text-red-300">UGX {{ number_format($this->summaryApproved, 0) }}</p>
            <p class="mt-1 text-xs text-red-600/60 dark:text-red-500">Confirmed spend</p>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-800/30 dark:bg-amber-900/10">
            <div class="flex size-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-800/30">
                <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-amber-700 dark:text-amber-400">Planned / Draft</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-amber-700 dark:text-amber-300">UGX {{ number_format($this->summaryDraft, 0) }}</p>
            <p class="mt-1 text-xs text-amber-600/60 dark:text-amber-500">Pending approval</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
            <div class="flex size-8 items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800">
                <svg class="size-4 text-zinc-600 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="mt-3 text-xs font-medium text-zinc-500 dark:text-zinc-400">This Month</p>
            <p class="mt-0.5 text-lg font-bold tabular-nums text-zinc-900 dark:text-zinc-100">UGX {{ number_format($this->summaryThisMonth, 0) }}</p>
            <p class="mt-1 text-xs text-zinc-400">{{ now()->format('F Y') }}</p>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-48">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search expenses…"
                class="w-full rounded-lg border border-zinc-200 bg-white py-2 pl-9 pr-4 text-sm text-zinc-900 placeholder-zinc-400 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />
        </div>
        <select wire:model.live="categoryFilter"
            class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700 shadow-sm focus:border-amber-400 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
            <option value="">All Categories</option>
            @foreach($this->categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="statusFilter"
            class="rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700 shadow-sm focus:border-amber-400 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
            <option value="">All Statuses</option>
            <option value="draft">Draft</option>
            <option value="approved">Approved</option>
        </select>
        <span class="text-xs text-zinc-400 dark:text-zinc-500">
            {{ $this->expenses->total() }} {{ Str::plural('record', $this->expenses->total()) }}
        </span>
    </div>

    {{-- Expenses Table --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-100 bg-zinc-50 dark:border-zinc-700/60 dark:bg-zinc-800/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Ref / Title</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Category</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Method</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Amount</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @forelse($this->expenses as $expense)
                    <tr class="group hover:bg-zinc-50/60 dark:hover:bg-zinc-800/30 transition-colors" wire:key="exp-{{ $expense->id }}">
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-xs font-semibold text-amber-600 dark:text-amber-400">{{ $expense->expense_number }}</span>
                            <p class="mt-0.5 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $expense->title }}</p>
                            @if($expense->reference)
                                <p class="text-xs text-zinc-400">Ref: {{ $expense->reference }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($expense->category)
                                <span class="inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ $expense->category->name }}
                                </span>
                            @else
                                <span class="text-xs text-zinc-400 dark:text-zinc-500">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-sm text-zinc-600 dark:text-zinc-400 whitespace-nowrap">
                            {{ $expense->expense_date->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-sm text-zinc-600 dark:text-zinc-400 capitalize">
                            {{ str_replace('_', ' ', $expense->payment_method) }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if($expense->status === 'approved')
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400">
                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>Approved
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">
                                    <span class="size-1.5 rounded-full bg-amber-500"></span>Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right tabular-nums font-semibold text-zinc-900 dark:text-zinc-100 whitespace-nowrap">
                            UGX {{ number_format($expense->amount, 0) }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @can('expenses.edit')
                                    <button wire:click="openEdit({{ $expense->id }})"
                                        class="rounded-lg p-1.5 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300 transition-colors"
                                        title="Edit">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                @endcan
                                @can('expenses.delete')
                                    <button wire:click="delete({{ $expense->id }})"
                                        wire:confirm="Delete this expense?"
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
                        <td colspan="7" class="px-5 py-16 text-center">
                            <svg class="mx-auto mb-3 size-10 text-zinc-200 dark:text-zinc-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            <p class="text-sm font-medium text-zinc-400 dark:text-zinc-500">No expenses recorded yet.</p>
                            @can('expenses.create')
                                <button wire:click="openCreate" class="mt-2 text-xs text-amber-600 hover:underline dark:text-amber-400">Record your first expense</button>
                            @endcan
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($this->expenses->hasPages())
        <div>{{ $this->expenses->links() }}</div>
    @endif

    {{-- ─── Expense Form Modal ─── --}}
    <flux:modal name="expense-form" class="md:w-[32rem]">
        <flux:heading size="lg">{{ $editingId ? 'Edit Expense' : 'Record Expense' }}</flux:heading>
        <flux:subheading>{{ $editingId ? 'Update the expense details below.' : 'Fill in the details to record a new expense.' }}</flux:subheading>

        <div class="mt-5 space-y-4">
            {{-- Title --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Title <span class="text-red-500">*</span></label>
                <input wire:model="title" type="text" placeholder="e.g. Office supplies purchase"
                    class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm placeholder-zinc-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />
                @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Category + Amount row --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Category</label>
                    <select wire:model="categoryId"
                        class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700 shadow-sm focus:border-amber-400 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                        <option value="">Uncategorized</option>
                        @foreach($this->categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Amount (UGX) <span class="text-red-500">*</span></label>
                    <input wire:model="amount" type="number" step="0.01" min="0" placeholder="0.00"
                        class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm placeholder-zinc-400 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />
                    @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Date + Method row --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Date <span class="text-red-500">*</span></label>
                    <input wire:model="expenseDate" type="date"
                        class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />
                    @error('expenseDate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Payment Method <span class="text-red-500">*</span></label>
                    <select wire:model="paymentMethod"
                        class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700 shadow-sm focus:border-amber-400 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
            </div>

            {{-- Reference + Status row --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Reference</label>
                    <input wire:model="reference" type="text" placeholder="Optional"
                        class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm placeholder-zinc-400 focus:border-amber-400 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Status <span class="text-red-500">*</span></label>
                    <select wire:model="status"
                        class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-700 shadow-sm focus:border-amber-400 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                        <option value="draft">Draft (Planned)</option>
                        <option value="approved">Approved (Actual)</option>
                    </select>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Notes</label>
                <textarea wire:model="notes" rows="2" placeholder="Optional notes…"
                    class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm placeholder-zinc-400 focus:border-amber-400 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"></textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <flux:button variant="ghost" x-on:click="$flux.modal('expense-form').close()">Cancel</flux:button>
            <flux:button variant="primary" wire:click="save" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Expense' : 'Record Expense' }}</span>
                <span wire:loading wire:target="save">Saving…</span>
            </flux:button>
        </div>
    </flux:modal>

    {{-- ─── Categories Modal ─── --}}
    <flux:modal name="expense-categories" class="md:w-[28rem]">
        <flux:heading size="lg">Expense Categories</flux:heading>
        <flux:subheading>Manage categories to group and filter expenses.</flux:subheading>

        {{-- Category Form --}}
        <div class="mt-5 space-y-3">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    {{ $editingCategoryId ? 'Edit Category' : 'New Category' }}
                </label>
                <input wire:model="categoryName" type="text" placeholder="Category name"
                    class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm placeholder-zinc-400 focus:border-amber-400 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />
                @error('categoryName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <input wire:model="categoryDescription" type="text" placeholder="Description (optional)"
                    class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm placeholder-zinc-400 focus:border-amber-400 focus:outline-none dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" />
            </div>
            <div class="flex gap-2">
                <flux:button variant="primary" wire:click="saveCategory" size="sm">
                    {{ $editingCategoryId ? 'Update' : 'Add Category' }}
                </flux:button>
                @if($editingCategoryId)
                    <flux:button variant="ghost" wire:click="cancelCategoryEdit" size="sm">Cancel</flux:button>
                @endif
            </div>
        </div>

        {{-- Categories List --}}
        <div class="mt-5 divide-y divide-zinc-100 rounded-lg border border-zinc-200 dark:divide-zinc-700/50 dark:border-zinc-700/60">
            @forelse($this->categories as $cat)
                <div class="flex items-center justify-between px-4 py-3" wire:key="cat-{{ $cat->id }}">
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $cat->name }}</p>
                        @if($cat->description)
                            <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ $cat->description }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-1">
                        <button wire:click="editCategory({{ $cat->id }})"
                            class="rounded p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                            title="Edit">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        @can('expenses.delete')
                            <button wire:click="deleteCategory({{ $cat->id }})"
                                class="rounded p-1 text-zinc-400 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                                title="Delete">
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="px-4 py-6 text-center text-sm text-zinc-400 dark:text-zinc-500">No categories yet. Add one above.</div>
            @endforelse
        </div>

        <div class="mt-5 flex justify-end">
            <flux:button variant="ghost" x-on:click="$flux.modal('expense-categories').close()">Done</flux:button>
        </div>
    </flux:modal>

</div>
