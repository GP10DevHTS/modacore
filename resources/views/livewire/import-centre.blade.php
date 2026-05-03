<div>
    {{-- Order your soul. Reduce your wants. - Augustine --}}
    <div class="mx-auto">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-zinc-800 dark:text-zinc-100">
                Import Centre
            </h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                Upload your initial stock using a formatted file. Download the template to get started.
            </p>
        </div>

        {{-- Card --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm p-6 space-y-6">

            {{-- File Upload --}}
            <div class="space-y-2">
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Select File
                </label>

                <div class="flex items-center gap-4">
                    <flux:input.file wire:model="file" class="flex-1" />

                    <flux:button
                        wire:click="getTemplate"
                        variant="outline"
                        class="shrink-0"
                    >
                        Download Template
                    </flux:button>
                </div>

                @error('file')
                <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Progress Bar --}}
            <div wire:loading wire:target="file" class="space-y-2">
                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 overflow-hidden">
                    <div class="bg-blue-500 h-2 animate-pulse w-full"></div>
                </div>
                <p class="text-xs text-zinc-500">preparing file...</p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button
                    wire:click="uploadFile"
                    variant="primary"
                    wire:loading.attr="disabled"
                    wire:target="uploadFile"
                >
                <span wire:loading.remove wire:target="uploadFile">
                    Upload File
                </span>
                    <span wire:loading wire:target="uploadFile">
                    Processing...
                </span>
                </flux:button>
            </div>

            {{-- Helper Info --}}
            <div class="text-xs text-zinc-500 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-800 p-3 rounded-lg">
                • Ensure your file follows the template format
                • Supported formats: CSV, XLSX
                • Large files may take longer to process
            </div>
        </div>
    </div>
</div>

