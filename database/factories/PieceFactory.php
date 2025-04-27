<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Piece>
 */
class PieceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'designation' => $this->faker->word(),
            // the reference should be unique
            'reference' => $this->faker->unique()->word(),
            'quantite_stock' => $this->faker->numberBetween(1, 100),
            'fournisseur' => $this->faker->word(),
            'prix_unitaire' => $this->faker->randomFloat(2, 1, 1000),
        ];
    }
}
