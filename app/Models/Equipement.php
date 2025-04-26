<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Ticket;
use App\Models\Bloc;
use Illuminate\Notifications\Notifiable;

class Equipement extends Model
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'bloc_id',
        'designation',
        'marque',
        'modele',
        'numero_serie',
        'date_acquisition',
        'date_mise_en_service',
        'etat',
        'fournisseur',
        'contact_fournisseur',
        'type_equipement',
        'date_fin_garantie',
        'sous_contrat',
        'type_contrat',
        'numero_contrat',
        'criticite'
    ];

    protected $casts = [
        'date_acquisition' => 'date',
        'date_mise_en_service' => 'date',
        'date_fin_garantie' => 'date',
        'sous_contrat' => 'boolean'
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
    public function bloc(): BelongsTo
    {
        return $this->belongsTo(Bloc::class, 'bloc_id');
    }

    public function maintenancePreventives(): HasMany
    {
        return $this->hasMany(MaintenancePreventive::class, 'equipement_id');
    }


}
