<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold">Import Centre</h2>
            <p class="text-gray-500">Manage initial stock imports and data templates.</p>
        </div>
        <flux:button wire:click="getTemplate" icon="arrow-down-tray">Download Template</flux:button>
    </div>

    <flux:card class="space-y-4">
        <h3 class="text-lg font-semibold">New Stock Import</h3>
        <form wire:submit="uploadFile" class="flex items-end gap-4">
            <div class="flex-1">
                <flux:input type="file" wire:model="file" label="Select Excel/CSV File" />
            </div>
            <flux:button type="submit" variant="primary" icon="cloud-arrow-up" wire:loading.attr="disabled">
                <span wire:loading.remove>Start Import</span>
                <span wire:loading>Importing...</span>
            </flux:button>
        </form>
    </flux:card>

    <flux:card class="space-y-4">
        <h3 class="text-lg font-semibold">Recent Imports</h3>
        <flux:table>
            <flux:columns>
                <flux:column>Date</flux:column>
                <flux:column>Filename</flux:column>
                <flux:column>User</flux:column>
                <flux:column>Status</flux:column>
                <flux:column align="end">Actions</flux:column>
            </flux:columns>
            <flux:rows>
                @foreach ($this->recentImports as $import)
                    <flux:row>
                        <flux:cell>{{ $import->created_at->format('M d, Y H:i') }}</flux:cell>
                        <flux:cell>{{ $import->filename }}</flux:cell>
                        <flux:cell>{{ $import->user->name }}</flux:cell>
                        <flux:cell>
                            @if ($import->status === 'completed')
                                <flux:badge color="green" inset="none">Completed</flux:badge>
                            @else
                                <flux:badge color="gray" inset="none">Reversed</flux:badge>
                            @endif
                        </flux:cell>
                        <flux:cell align="end">
                            @if ($import->status === 'completed')
                                <flux:button
                                    size="sm"
                                    variant="danger"
                                    icon="arrow-path"
                                    wire:click="reverseImport({{ $import->id }})"
                                    wire:confirm="Are you sure you want to reverse this import? This will delete all records created by it."
                                >
                                    Reverse
                                </flux:button>
                            @else
                                <span class="text-xs text-gray-400">Reversed at {{ $import->reversed_at->format('M d, H:i') }}</span>
                            @endif
                        </flux:cell>
                    </flux:row>
                @endforeach
            </flux:rows>
        </flux:table>
    </flux:card>
</div>
