<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Equipement;
use Illuminate\Notifications\Notifiable;

class Bloc extends Model
{
    use HasFactory;
    use Notifiable;


    protected $fillable = [
        'nom_bloc',
        'description',
        'type_bloc_id',
        'localisation',
    ];

    public function typeBloc(): BelongsTo
    {
        return $this->belongsTo(TypeBloc::class);
    }

    public function equipements(): HasMany
    {
        return $this->hasMany(Equipement::class);
    }
}
