<?php

namespace App\Livewire\Notifications;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Center extends Component
{
    use WithPagination;

    public string $filter = 'all';

    #[Computed]
    public function notifications()
    {
        $query = auth()->user()->notifications()->latest();

        return match ($this->filter) {
            'unread' => $query->whereNull('read_at')->paginate(20),
            'read' => $query->whereNotNull('read_at')->paginate(20),
            default => $query->paginate(20),
        };
    }

    #[Computed]
    public function unreadCount(): int
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function markRead(string $id): void
    {
        auth()->user()->notifications()->where('id', $id)->first()?->markAsRead();
        unset($this->notifications, $this->unreadCount);
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        unset($this->notifications, $this->unreadCount);
    }

    public function delete(string $id): void
    {
        auth()->user()->notifications()->where('id', $id)->delete();
        unset($this->notifications, $this->unreadCount);
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.notifications.center');
    }
}
