<?php

namespace Database\Factories;

use App\Models\TypeBloc;
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
        return [
            'nom_bloc' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'type_bloc_id' => TypeBloc::factory(),
            'localisation' => $this->faker->randomElement(['Nord', 'Sud', 'Est', 'Ouest']) . ' - ' .
                $this->faker->numberBetween(1, 5) . 'ème étage',
        ];
    }
}
