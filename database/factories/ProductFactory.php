<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // define um gerador de produtos para teste
        // não existe função no faker para criar especificamente nome de produto, então usei `word`
        return [
            'name' => fake()->word(),
            'stock' => fake()->randomFloat(2, 0, 100),
            'price' => fake()->randomFloat(2, 0, 100),
        ];
    }
}
