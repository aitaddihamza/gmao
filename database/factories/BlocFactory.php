<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bloc>
 */
class BlocFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // définir les valeurs par défaut pour les attributs de la table blocs
        return [
            'nom_bloc' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'type_bloc' => $this->faker->word(),
            'localisation' => $this->faker->address(),
        ];
    }
}
