<?php

namespace App\Livewire\Dashboard;

use App\Models\PoAuditLog;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AuditSummary extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()->can('inventory.view'), 403);
    }

    #[Computed]
    public function logs()
    {
        return PoAuditLog::query()
            ->with(['purchaseOrder.supplier', 'user'])
            ->latest()
            ->limit(15)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.audit-summary');
    }
}
