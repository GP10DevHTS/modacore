@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-1">
    <flux:heading size="xl" class="text-zinc-900 dark:text-zinc-100">{{ $title }}</flux:heading>
    <flux:subheading class="text-zinc-500 dark:text-zinc-400">{{ $description }}</flux:subheading>
</div>
