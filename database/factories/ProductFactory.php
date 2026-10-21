<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Fresh Milk',
                'Low-Fat Milk',
                'Yogurt',
                'Butter',
                'Cheddar Cheese',
                'Cream',
                'Buttermilk',
                'Paneer',
                'Ghee',
                'Mozzarella Cheese',
            ]),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 100, 1000),
            'unit' => fake()->randomElement([
                'litre',
                'bottle',
                'kg',
                'pack',
            ]),
            'stock' => fake()->numberBetween(10, 200),
            'is_available' => true,
        ];
    }
}