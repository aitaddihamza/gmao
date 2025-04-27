<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeBloc extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
    ];

    public function blocs(): HasMany
    {
        return $this->hasMany(Bloc::class);
    }
} 