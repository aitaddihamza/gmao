<?php

namespace Database\Factories;

use App\Models\TypeEquipement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TypeEquipement>
 */
class TypeEquipementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TypeEquipement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            'nom' => $this->faker->unique()->word() . ' ' . $this->faker->word(),
        ];
    }
}
