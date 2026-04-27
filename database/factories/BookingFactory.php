<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hireFrom = fake()->dateTimeBetween('now', '+30 days');
        $hireTo = fake()->dateTimeBetween($hireFrom, '+40 days');

        return [
            'booking_number' => 'BK-' . now()->format('Ymd') . '-' . str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'customer_id' => \App\Models\Customer::factory(),
            'hire_from' => $hireFrom,
            'hire_to' => $hireTo,
            'status' => fake()->randomElement(['draft', 'confirmed', 'active', 'completed', 'cancelled']),
            'total_amount' => fake()->randomFloat(2, 50, 2000),
            'notes' => fake()->optional()->sentence(),
            'created_by' => 1,
        ];
    }
}
