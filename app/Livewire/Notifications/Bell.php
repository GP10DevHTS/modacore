<?php

namespace App\Livewire\Notifications;

use Livewire\Attributes\Computed;
use Livewire\Component;

class Bell extends Component
{
    #[Computed]
    public function unreadCount(): int
    {
        return auth()->user()->unreadNotifications()->count();
    }

    #[Computed]
    public function recent()
    {
        return auth()->user()->notifications()->latest()->limit(6)->get();
    }

    public function markRead(string $id): void
    {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
        unset($this->unreadCount, $this->recent);
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        unset($this->unreadCount, $this->recent);
    }

    public function render()
    {
        return view('livewire.notifications.bell');
    }
}
