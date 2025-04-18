<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Equipement;
use Illuminate\Notifications\Notifiable;

class Bloc extends Model
{
    use HasFactory;
    use Notifiable;


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
