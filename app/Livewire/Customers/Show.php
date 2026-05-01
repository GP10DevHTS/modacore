<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\CustomerMeasurement;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public Customer $customer;

    // Measurement form
    public string $chest = '';

    public string $waist = '';

    public string $hips = '';

    public string $shoulderWidth = '';

    public string $sleeveLength = '';

    public string $inseam = '';

    public string $neck = '';

    public string $height = '';

    public function mount(Customer $customer): void
    {
        $this->customer = $customer->load(['measurements', 'bookings.items']);
        $this->fillMeasurements();
    }

    private function fillMeasurements(): void
    {
        $m = $this->customer->measurements;
        $this->chest = $m?->chest ? (string) $m->chest : '';
        $this->waist = $m?->waist ? (string) $m->waist : '';
        $this->hips = $m?->hips ? (string) $m->hips : '';
        $this->shoulderWidth = $m?->shoulder_width ? (string) $m->shoulder_width : '';
        $this->sleeveLength = $m?->sleeve_length ? (string) $m->sleeve_length : '';
        $this->inseam = $m?->inseam ? (string) $m->inseam : '';
        $this->neck = $m?->neck ? (string) $m->neck : '';
        $this->height = $m?->height ? (string) $m->height : '';
    }

    #[Computed]
    public function financialSummary(): array
    {
        $bookings = $this->customer->bookings()
            ->whereNotIn('status', ['cancelled'])
            ->withSum('payments', 'amount')
            ->get();

        $totalBooked = (float) $bookings->sum('total_amount');
        $totalPaid = (float) $bookings->sum('payments_sum_amount');
        $outstanding = max(0, $totalBooked - $totalPaid);

        return compact('totalBooked', 'totalPaid', 'outstanding');
    }

    #[Computed]
    public function bookings()
    {
        return $this->customer->bookings()
            ->withSum('payments', 'amount')
            ->latest()
            ->get();
    }

    public function saveMeasurements(): void
    {
        abort_unless(auth()->user()->can('customers.edit'), 403);

        $this->validate([
            'chest' => ['nullable', 'numeric', 'min:0'],
            'waist' => ['nullable', 'numeric', 'min:0'],
            'hips' => ['nullable', 'numeric', 'min:0'],
            'shoulderWidth' => ['nullable', 'numeric', 'min:0'],
            'sleeveLength' => ['nullable', 'numeric', 'min:0'],
            'inseam' => ['nullable', 'numeric', 'min:0'],
            'neck' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
        ]);

        $data = [
            'chest' => $this->chest ?: null,
            'waist' => $this->waist ?: null,
            'hips' => $this->hips ?: null,
            'shoulder_width' => $this->shoulderWidth ?: null,
            'sleeve_length' => $this->sleeveLength ?: null,
            'inseam' => $this->inseam ?: null,
            'neck' => $this->neck ?: null,
            'height' => $this->height ?: null,
        ];

        CustomerMeasurement::updateOrCreate(
            ['customer_id' => $this->customer->id],
            $data,
        );

        $this->customer->refresh()->load('measurements');
        $this->fillMeasurements();

        Flux::toast(text: 'Measurements saved.', variant: 'success');
    }

    public function render()
    {
        return view('livewire.customers.show');
    }
}
