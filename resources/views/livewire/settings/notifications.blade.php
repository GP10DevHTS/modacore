<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Notifications')" :subheading="__('Schedule when you want to receive due-return reminders')">
        <form wire:submit="save" class="my-6 w-full space-y-6">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Return reminder time</label>
                <p class="mb-3 text-xs text-zinc-500 dark:text-zinc-400">
                    Choose the hour you'd like to be reminded about bookings due for return that day.
                    The system checks every hour — alerts will only fire at your chosen hour.
                    Leave unset to receive reminders every hour.
                </p>
                <select wire:model="reminderHour"
                    class="block w-full rounded-lg border border-zinc-200 bg-white px-3 py-2 text-sm text-zinc-900 shadow-sm focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                    <option value="">— Every hour (no preference) —</option>
                    @foreach($this->hourOptions as $opt)
                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                    @endforeach
                </select>
                <flux:error name="reminderHour" />
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </x-settings.layout>
</section>
