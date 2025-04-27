<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeEquipement extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
    ];

    public function equipements(): HasMany
    {
        return $this->hasMany(Equipement::class);
    }
}
