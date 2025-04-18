<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use APp\Models\Equipement;

class Bloc extends Model
{
    protected $fillable = [
        'nom_bloc',
        'description',
        'type_bloc',
        'localisation',
    ];


    public function equipements()
    {
        return $this->hasMany(Equipement::class, 'bloc_id');
    }
}
