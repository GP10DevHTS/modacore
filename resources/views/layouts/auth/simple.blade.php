<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 antialiased dark:bg-zinc-950">
        <div class="flex min-h-screen flex-col items-center justify-center p-6">
            <div class="w-full max-w-sm space-y-6">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2" wire:navigate>
                    <span class="flex size-10 items-center justify-center rounded-xl bg-amber-500 shadow-sm">
                        <x-app-logo-icon class="size-6 fill-current text-black" />
                    </span>
                    <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ config('app.name', 'ModaCore') }}</span>
                </a>
                <div class="rounded-2xl border border-zinc-200 bg-white px-8 py-8 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
