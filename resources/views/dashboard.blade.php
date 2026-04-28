<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-6">

        {{-- Welcome --}}
        <div>
            <h1 class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
                Welcome back, {{ auth()->user()->name }}
            </h1>
            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Here's a quick overview of your operations.</p>
        </div>

        @can('inventory.edit')
        {{-- Procurement Summary (live from Livewire) --}}
        @livewire('purchase-orders.procurement-dashboard')
        @endcan

    </div>
</x-layouts::app>
