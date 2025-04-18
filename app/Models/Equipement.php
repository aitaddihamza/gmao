<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Ticket;
use App\Models\Bloc;

class Equipement extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'reference',
        'description',
        'marque',
        'modele',
        'numero_serie',
        'date_acquisition',
        'date_mise_en_service',
        'etat',
        'localisation',
        'qr_code',
        'criticite',
    ];

    protected $casts = [
        'date_acquisition' => 'datetime',
        'date_mise_en_service' => 'datetime',
    ];

    // Relations
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // Méthodes utiles
    public function getFullDesignation(): string
    {
        return "{$this->marque} {$this->modele} - {$this->numero_serie}";
    }

    public function isUnderMaintenance(): bool
    {
        return $this->tickets()->where('statut', '!=', 'cloture')
            ->where('type_ticket', 'maintenance')
            ->exists();
    }
    public function bloc()
    {
        return $this->belongsTo(Bloc::class, 'bloc_id');
    }
}
