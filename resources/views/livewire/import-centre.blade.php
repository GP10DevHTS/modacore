<div>
    <div class="mx-auto max-w-4xl space-y-8">

        {{-- Header --}}
        <div>
            <h1 class="text-xl font-semibold text-zinc-800 dark:text-zinc-100">
                Import Centre
            </h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                Download pre-formatted templates to prepare your data, then upload the completed files for bulk import.
            </p>
        </div>

        {{-- ═══ DOWNLOAD TEMPLATES ═══ --}}
        <div class="space-y-4">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                1. Download Templates
            </h2>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Variant Types --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Variant Types</h3>
                            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Define variant dimensions like Size, Color</p>
                            <span class="mt-1 inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-medium text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">Name, Sort Order</span>
                        </div>
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/20">
                            <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
                            </svg>
                        </div>
                    </div>
                    <flux:button wire:click="downloadVariantTypes" variant="outline" size="sm" class="mt-4 w-full">
                        Download .xlsx
                    </flux:button>
                </div>

                {{-- Variant Type Values --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Variant Values</h3>
                            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Add values for each variant type</p>
                            <span class="mt-1 inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-medium text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">Variant Type, Label, Sort Order</span>
                        </div>
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/20">
                            <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                    </div>
                    <flux:button wire:click="downloadVariantTypeValues" variant="outline" size="sm" class="mt-4 w-full">
                        Download .xlsx
                    </flux:button>
                </div>

                {{-- Categories --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Categories</h3>
                            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Organise inventory by category</p>
                            <span class="mt-1 inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-medium text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">Name, Description, Code</span>
                        </div>
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/20">
                            <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                            </svg>
                        </div>
                    </div>
                    <flux:button wire:click="downloadCategories" variant="outline" size="sm" class="mt-4 w-full">
                        Download .xlsx
                    </flux:button>
                </div>

                {{-- Items --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Inventory Items</h3>
                            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">List your rentable products</p>
                            <span class="mt-1 inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-medium text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">Name, Category, Prices, Description</span>
                        </div>
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/20">
                            <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                    <flux:button wire:click="downloadItems" variant="outline" size="sm" class="mt-4 w-full">
                        Download .xlsx
                    </flux:button>
                </div>

                {{-- Variations --}}
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Product Variations</h3>
                            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">Variant combinations with prices &amp; stock</p>
                            <span class="mt-1 inline-flex items-center rounded-full bg-zinc-100 px-2 py-0.5 text-[10px] font-medium text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">Item, SKU, Variants..., Prices</span>
                        </div>
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-900/20">
                            <svg class="size-4 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.5-5.5a2.5 2.5 0 013.54-3.53l5.5 5.5M15.5 8.5L19 12l-3.5 3.5M12 5.5l-1.5-1.5M18.5 14.5l-1.5 1.5m-8-8l-1.5-1.5"/>
                            </svg>
                        </div>
                    </div>
                    <flux:button wire:click="downloadVariations" variant="outline" size="sm" class="mt-4 w-full">
                        Download .xlsx
                    </flux:button>
                </div>

                {{-- Combined Stock Template --}}
                <div class="rounded-xl border border-amber-200 bg-amber-50/30 p-5 shadow-sm dark:border-amber-800/30 dark:bg-amber-900/10">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Combined Stock Template</h3>
                            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">All-in-one: items, categories &amp; variants</p>
                            <span class="mt-1 inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-medium text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Item, Category, Variants, Qty, Prices</span>
                        </div>
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-800/30">
                            <svg class="size-4 text-amber-700 dark:text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                        </div>
                    </div>
                    <flux:button wire:click="getTemplate" variant="primary" size="sm" class="mt-4 w-full">
                        Download .xlsx
                    </flux:button>
                </div>
            </div>
        </div>

        {{-- ═══ UPLOAD SECTION ═══ --}}
        <div class="space-y-4">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                2. Upload Completed File
            </h2>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700/60 dark:bg-zinc-900">
                {{-- Import Type Selector --}}
                <div class="mb-5">
                    <label class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Import Type
                    </label>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach ([
                            'stock' => ['label' => 'Combined Stock', 'desc' => 'Items, cats & variants', 'color' => 'amber'],
                            'variant-types' => ['label' => 'Variant Types', 'desc' => 'Define dimensions', 'color' => 'zinc'],
                            'variant-values' => ['label' => 'Variant Values', 'desc' => 'Add values', 'color' => 'zinc'],
                            'categories' => ['label' => 'Categories', 'desc' => 'Organise inventory', 'color' => 'zinc'],
                            'items' => ['label' => 'Inventory Items', 'desc' => 'Rentable products', 'color' => 'zinc'],
                            'variations' => ['label' => 'Product Variations', 'desc' => 'Variant combos', 'color' => 'zinc'],
                        ] as $key => $info)
                            <button wire:click="$set('importType', '{{ $key }}')" type="button"
                                class="relative rounded-lg border px-3 py-2.5 text-left text-xs transition-all
                                    {{ $importType === $key
                                        ? 'border-amber-400 bg-amber-50 ring-1 ring-amber-400/30 dark:border-amber-500 dark:bg-amber-900/15'
                                        : 'border-zinc-200 bg-white hover:border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-zinc-600' }}">
                                <div class="font-medium {{ $importType === $key ? 'text-amber-800 dark:text-amber-300' : 'text-zinc-800 dark:text-zinc-200' }}">
                                    {{ $info['label'] }}
                                </div>
                                <div class="mt-0.5 {{ $importType === $key ? 'text-amber-600/70 dark:text-amber-400/70' : 'text-zinc-400 dark:text-zinc-500' }}">
                                    {{ $info['desc'] }}
                                </div>
                            </button>
                        @endforeach
                    </div>
                    @error('importType')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- File Upload --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Select File
                    </label>

                    <div class="flex items-center gap-4">
                        <flux:input.file wire:model="file" class="flex-1" />
                    </div>

                    @error('file')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Progress Bar --}}
                <div wire:loading wire:target="file" class="mt-4 space-y-2">
                    <div class="h-2 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                        <div class="h-2 w-full animate-pulse bg-blue-500"></div>
                    </div>
                    <p class="text-xs text-zinc-500">preparing file...</p>
                </div>

                {{-- Actions --}}
                <div class="mt-4 flex items-center justify-end gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-800">
                    <flux:button
                        wire:click="uploadFile"
                        variant="primary"
                        wire:loading.attr="disabled"
                        wire:target="uploadFile"
                    >
                    <span wire:loading.remove wire:target="uploadFile">
                        Upload &amp; Import
                    </span>
                        <span wire:loading wire:target="uploadFile">
                        Processing...
                    </span>
                    </flux:button>
                </div>
            </div>
        </div>

        {{-- Helper Info --}}
        <div class="rounded-lg bg-zinc-50 p-4 text-xs text-zinc-500 dark:bg-zinc-800/50 dark:text-zinc-400">
            <p class="mb-1 font-medium">Tips:</p>
            <ul class="list-inside list-disc space-y-1">
                <li>Download each template and fill in your data following the column headers</li>
                <li>Select the matching <strong>Import Type</strong> before uploading your completed file</li>
                <li>Large files may take longer to process — max file size is 10MB</li>
                <li>Supported formats: CSV, XLSX</li>
            </ul>
        </div>
    </div>
</div>
