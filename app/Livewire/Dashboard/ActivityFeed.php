<?php

namespace App\Livewire\Dashboard;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PoAuditLog;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ActivityFeed extends Component
{
    #[Computed]
    public function activities(): Collection
    {
        $user = auth()->user();
        $events = collect();

        if ($user->can('bookings.view')) {
            $bookings = Booking::query()
                ->with(['customer', 'createdBy'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn ($b) => [
                    'type' => 'booking',
                    'time' => $b->created_at,
                    'title' => "Booking {$b->booking_number}",
                    'description' => ($b->customer?->name ?? 'Unknown').' • '.ucfirst($b->status),
                    'url' => route('bookings.show', $b->id),
                    'icon_color' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400',
                    'actor' => $b->createdBy?->name,
                ]);
            $events = $events->merge($bookings);
        }

        if ($user->can('payments.view')) {
            $payments = Payment::query()
                ->with(['booking.customer', 'createdBy'])
                ->whereNotNull('paid_at')
                ->orderByDesc('paid_at')
                ->limit(5)
                ->get()
                ->map(fn ($p) => [
                    'type' => 'payment',
                    'time' => $p->paid_at ?? $p->created_at,
                    'title' => 'Payment received — UGX '.number_format((float) $p->amount, 0),
                    'description' => ($p->booking?->customer?->name ?? 'Unknown').' • '.ucfirst(str_replace('_', ' ', $p->payment_method)).($p->is_deposit ? ' (Deposit)' : ''),
                    'url' => $p->booking_id ? route('bookings.show', $p->booking_id) : '#',
                    'icon_color' => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400',
                    'actor' => $p->createdBy?->name,
                ]);
            $events = $events->merge($payments);
        }

        if ($user->can('inventory.view')) {
            $auditLogs = PoAuditLog::query()
                ->with(['purchaseOrder', 'user'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn ($a) => [
                    'type' => 'audit',
                    'time' => $a->created_at,
                    'title' => 'PO '.($a->purchaseOrder?->po_number ?? '#').' — '.ucfirst(str_replace('_', ' ', $a->action)),
                    'description' => $a->description ?? ucfirst(str_replace('_', ' ', $a->action)),
                    'url' => $a->purchase_order_id ? route('purchase-orders.show', $a->purchase_order_id) : '#',
                    'icon_color' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400',
                    'actor' => $a->user?->name,
                ]);
            $events = $events->merge($auditLogs);
        }

        return $events->sortByDesc('time')->take(10)->values();
    }

    public function render()
    {
        return view('livewire.dashboard.activity-feed');
    }
}
