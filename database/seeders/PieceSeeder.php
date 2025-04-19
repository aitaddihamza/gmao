<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Piece;

class PieceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // créer 30 pièces
        for ($i = 0; $i < 30; $i++) {
            Piece::factory()->create([
                'designation' => 'Piece ' . ($i + 1),
                'reference' => 'REF' . ($i + 1),
                'quantite_stock' => rand(1, 100),
                'fournisseur' => 'Fournisseur ' . ($i + 1),
                'prix_unitaire' => rand(10, 1000),
            ]);
        }
    }
}
