<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'title' => fake()->sentence(2),
            'order' => fake()->numberBetween(1, 99),
            'description' => fake()->paragraphs(2, true),
        ];
    }
}
