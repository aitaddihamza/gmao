<?php

namespace Database\Seeders;

use App\Models\TypeEquipement;
use Illuminate\Database\Seeder;

class TypeEquipementSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'imagerie',
            'néphrologie',
            'chirurgie',
            'réanimation',
            'cardiologie',
            'neurologie',
            'obstétrique',
            'anesthésie',
            'laboratoire',
            'dentaire',
            'ophtalmologie',
            'dermatologie',
            'gynécologie',
            'pédiatrie',
            'urgences'
        ];

        foreach ($types as $type) {
            TypeEquipement::create([
                'nom' => $type,
            ]);
        }
    }
} 