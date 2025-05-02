<?php

namespace Database\Seeders;

use App\Models\TypeBloc;
use App\Models\User;
use App\Models\Bloc;
use App\Models\Equipement;
use App\Models\TypeEquipement;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Piece;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TypeEquipementSeeder::class,
            TypeBlocSeeder::class,
            UserSeeder::class,
        ]);

        // Créer l'utilisateur admin
        User::factory()->create([
            'name' => 'admin',
            'prenom' => 'admin',
            'role' => 'admin',
            'email' => 'admin@gmail.com',
        ]);

        User::factory()->create([
            'name' => 'ahmed',
            'prenom' => 'abcde',
            'email' => 'ahmed@gmail.com',
            'role' => 'responsable'
        ]);

        // Créer 10 blocs avec des données différentes
        for ($i = 1; $i < 10; $i++) {
            Bloc::factory()->count(1)->create([
                'type_bloc_id' => rand(1, 10),
            ]);
        }

        // Créer 30 pièces
        Piece::factory()->count(30)->create();

        // Créer 30 équipements médicaux
        Equipement::factory()->count(30)->create();
    }
}
