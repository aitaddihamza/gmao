<?php

namespace Database\Factories;

use App\Models\Bloc;
use App\Models\TypeEquipement;
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

        // Générer une date de fin de garantie entre maintenant et 3 ans dans le futur
        $dateFinGarantie = $this->faker->dateTimeBetween('now', '+3 years');

        return [
            'bloc_id' => rand(1, 9),
            'type_equipement_id' => rand(1, 15),
            'designation' => $this->faker->randomElement([
                'Appareil à ECG', 'Échographe', 'Défibrillateur', 'Respirateur',
                'Scanner', 'IRM', 'Moniteur de signes vitaux', 'Pompe à perfusion',
                'Analyseur de gaz du sang', 'Dialyseur', 'Table opératoire', 'Endoscope'
            ]),
            'marque' => $this->faker->company(),
            'modele' => $this->faker->word() . '-' . $this->faker->numberBetween(100, 999),
            'numero_serie' => 'SN-' . $this->faker->unique()->regexify('[A-Z]{3}[0-9]{4}'),
            'date_acquisition' => $dateAcquisition,
            'date_mise_en_service' => $dateMiseEnService,
            'etat' => $this->faker->randomElement(['bon', 'acceptable', 'mauvais', 'hors_service']),
            'fournisseur' => $this->faker->company(),
            'contact_fournisseur' => $this->faker->phoneNumber(),
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
