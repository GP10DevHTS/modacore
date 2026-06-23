@props([
    'status',
])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-zinc-600 dark:text-zinc-400']) }}>
        {{ $status }}
    </div>
@endif
