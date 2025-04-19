<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bloc;
use App\Models\Equipement;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer l'utilisateur admin
        User::factory()->create([
            'name' => 'admin',
            'prenom' => 'admin',
            'email' => 'admin@gmail.com',
        ]);

        User::factory()->create([
            'name' => 'ahmed',
            'prenom' => 'abcde',
            'email' => 'ahmed@gmail.com',
            'role' => 'responsable'
        ]);

        // Créer 10 blocs avec des données différentes
        for ($i = 1; $i <= 10; $i++) {
            Bloc::factory()->create([
                'nom_bloc' => "Bloc $i",
                'description' => "Description du bloc $i",
                'type_bloc' => "Type " . fake()->randomElement(['A', 'B', 'C']),
                'localisation' => "Étage " . fake()->numberBetween(1, 5) . ", " .
                    fake()->randomElement(['Aile Nord', 'Aile Sud', 'Aile Est', 'Aile Ouest']),
            ]);
        }

        // Créer 30 équipements médicaux
        for ($i = 1; $i <= 30; $i++) {
            Equipement::factory()->create([
                'bloc_id' => fake()->numberBetween(1, 10),
                'numero_serie' => 'SN-' . $i . '-' . fake()->unique()->regexify('[A-Z]{3}[0-9]{4}'),
                'designation' => fake()->randomElement([
                    'Appareil à ECG', 'Échographe', 'Défibrillateur', 'Respirateur',
                    'Scanner', 'IRM', 'Moniteur de signes vitaux', 'Pompe à perfusion',
                    'Analyseur de gaz du sang', 'Dialyseur', 'Table opératoire', 'Endoscope'
                ]) . ' ' . $i,
                'etat' => fake()->randomElement(['bon', 'acceptable', 'mauvais', 'hors_service']),
                'criticite' => fake()->randomElement(['haute', 'moyenne', 'basse']),
                'date_acquisition' => fake()->dateTimeBetween('-5 years', 'now'),
                'date_mise_en_service' => fake()->dateTimeBetween('-4 years', 'now'),
                'date_fin_garantie' => fake()->dateTimeBetween('now', '+3 years'),
                'type_equipement' => fake()->randomElement([
                    'imagerie', 'néphrologie', 'chirurgie', 'réanimation',
                    'cardiologie', 'neurologie', 'obstétrique', 'anesthésie'
                ]),
            ]);
        }
    }
}
