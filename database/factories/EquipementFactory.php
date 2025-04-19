<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Equipement>
 */
class EquipementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Générer une date d'acquisition entre 5 ans dans le passé et aujourd'hui
        $dateAcquisition = $this->faker->dateTimeBetween('-5 years', 'now');

        // Générer une date de mise en service entre la date d'acquisition et aujourd'hui
        $dateMiseEnService = $this->faker->dateTimeBetween($dateAcquisition, 'now');

        $dateFinGarantie = Carbon::parse($dateAcquisition)->addMonths(rand(12, 36));

        // Définir les valeurs par défaut pour les attributs de la table equipements
        return [
            'bloc_id' => \App\Models\Bloc::factory(),
            'designation' => $this->faker->word(),
            'marque' => $this->faker->company(),
            'modele' => $this->faker->word() . '-' . $this->faker->numberBetween(100, 999),
            'numero_serie' => 'EQ-' . $this->faker->unique()->regexify('[A-Z]{2}[0-9]{6}'),
            'date_acquisition' => $dateAcquisition,
            'date_mise_en_service' => $dateMiseEnService,
            'etat' => $this->faker->randomElement(['bon', 'acceptable', 'mauvais', 'hors_service']),
            'fournisseur' => $this->faker->company(),
            'contact_fournisseur' => $this->faker->phoneNumber(),
            'type_equipement' => $this->faker->randomElement([
                'imagerie', 'néphrologie', 'chirurgie', 'réanimation',
                'cardiologie', 'neurologie', 'obstétrique', 'anesthésie'
            ]),
            'date_fin_garantie' => $dateFinGarantie,
            'sous_contrat' => $this->faker->boolean(),
            'type_contrat' => function (array $attributes) {
                return $attributes['sous_contrat'] ? $this->faker->randomElement(['maintenance', 'location', 'leasing']) : null;
            },
            'numero_contrat' => function (array $attributes) {
                return $attributes['sous_contrat'] ? 'CONT-' . $this->faker->regexify('[A-Z]{2}[0-9]{4}') : null;
            },
            'criticite' => $this->faker->randomElement(['haute', 'moyenne', 'basse'])
        ];
    }
}
