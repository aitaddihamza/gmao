<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Ticket;
use App\Models\Bloc;
use App\Models\MaintenancePreventive;
use Illuminate\Notifications\Notifiable;
use App\Models\TypeEquipement;
use Illuminate\Support\Facades\Storage;

class Equipement extends Model
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'bloc_id',
        'type_equipement_id',
        'designation',
        'marque',
        'modele',
        'numero_serie',
        'date_acquisition',
        'date_mise_en_service',
        'etat',
        'fournisseur',
        'contact_fournisseur',
        'date_fin_garantie',
        'sous_contrat',
        'type_contrat',
        'numero_contrat',
        'criticite',
        'manuel_path'
    ];

    protected $casts = [
        'date_acquisition' => 'date',
        'date_mise_en_service' => 'date',
        'date_fin_garantie' => 'date',
        'sous_contrat' => 'boolean'
    ];

    protected static function booted()
    {
        static::saving(function ($equipement) {
            // Si le manuel_path a changé et qu'il y avait un ancien manuel
            if ($equipement->isDirty('manuel_path') && $equipement->getOriginal('manuel_path')) {
                $oldPath = $equipement->getOriginal('manuel_path');
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        });

        static::deleting(function ($equipement) {
            // Supprimer le manuel lors de la suppression de l'équipement
            if ($equipement->manuel_path && Storage::disk('public')->exists($equipement->manuel_path)) {
                Storage::disk('public')->delete($equipement->manuel_path);
            }
        });
    }

    // Relations

    public function bloc(): BelongsTo
    {
        return $this->belongsTo(Bloc::class);
    }

    public function typeEquipement(): BelongsTo
    {
        return $this->belongsTo(TypeEquipement::class);
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

    public function maintenancePreventives(): HasMany
    {
        return $this->hasMany(MaintenancePreventive::class);
    }

}
