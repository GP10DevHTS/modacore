<?php

namespace App\Livewire\Settings;

use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Notification settings')]
class Notifications extends Component
{
    public ?string $reminderHour = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->reminderHour = $user->reminder_hour !== null ? (string) $user->reminder_hour : null;
    }

    public function save(): void
    {
        $this->validate([
            'reminderHour' => ['nullable', 'integer', 'min:0', 'max:23'],
        ]);

        auth()->user()->update([
            'reminder_hour' => $this->reminderHour !== null ? (int) $this->reminderHour : null,
        ]);

        Flux::toast(variant: 'success', text: 'Notification preferences saved.');
    }

    #[Computed]
    public function hourOptions(): array
    {
        $options = [];
        for ($i = 0; $i < 24; $i++) {
            $label = str_pad($i, 2, '0', STR_PAD_LEFT).':00';
            $options[] = ['value' => (string) $i, 'label' => $label];
        }

        return $options;
    }

    public function render()
    {
        return view('livewire.settings.notifications');
    }
}
