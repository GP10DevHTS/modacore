<?php

namespace Database\Factories;

use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ExpenseCategory> */
class ExpenseCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Utilities', 'Transport', 'Marketing', 'Office Supplies',
                'Maintenance', 'Salaries', 'Rent', 'Equipment', 'Training', 'Other',
            ]),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
