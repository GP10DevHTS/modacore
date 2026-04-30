<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Expense> */
class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        static $seq = 0;
        $seq++;

        return [
            'expense_number' => sprintf('EXP-%d-%04d', now()->year, $seq),
            'category_id' => null,
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'amount' => fake()->randomFloat(2, 5_000, 500_000),
            'expense_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'payment_method' => fake()->randomElement(['cash', 'card', 'mobile_money']),
            'reference' => fake()->optional()->bothify('REF-####'),
            'status' => 'draft',
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }

    public function approved(): static
    {
        return $this->state(['status' => 'approved']);
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }
}
