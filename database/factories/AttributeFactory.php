<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(2),
        ];
    }
}
