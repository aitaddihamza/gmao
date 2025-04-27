<?php

namespace Database\Seeders;

use App\Models\TypeBloc;
use Illuminate\Database\Seeder;

class TypeBlocSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Bloc opératoire',
            'Bloc d\'accouchement',
            'Bloc de réanimation',
            'Bloc de cardiologie',
            'Bloc d\'imagerie',
            'Bloc de dialyse',
            'Bloc de consultation',
            'Bloc d\'urgences',
            'Bloc de pédiatrie',
            'Bloc de gynécologie',
        ];

        foreach ($types as $type) {
            TypeBloc::firstOrCreate(
                ['nom' => $type]
            );
        }
    }
} 