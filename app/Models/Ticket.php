<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipement_id',
        'user_createur_id',
        'user_assignee_id',
        'type_ticket',
        'statut',
        'priorite',
        'description',
        'chemin_image',
        'rapport_path',
        'rapport_type',
        'solution',
        'diagnostic',
        'fournisseur',
        'date_resolution',
        'recommandations',
        'date_intervention',
        'type_externe',
        'gravite_panne',
        'temps_arret'
    ];

    protected $casts = [
        'date_intervention' => 'datetime',
        'date_resolution' => 'datetime',
        'chemin_image' => 'array',
        'type_externe' => 'boolean',
        'statut' => 'string',
        'type_ticket' => 'string',
        'priorite' => 'string',
        'gravite_panne' => 'string',
        'rapport_type' => 'string',
    ];

    protected static function booted()
    {
        static::saving(function ($ticket) {
            // Si le rapport_path a changé et qu'il y avait un ancien rapport
            if ($ticket->isDirty('rapport_path') && $ticket->getOriginal('rapport_path')) {
                $oldPath = $ticket->getOriginal('rapport_path');
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Si les images ont changé et qu'il y avait d'anciennes images
            if ($ticket->isDirty('chemin_image') && $ticket->getOriginal('chemin_image')) {
                $oldImages = $ticket->getOriginal('chemin_image');
                if (is_array($oldImages)) {
                    foreach ($oldImages as $oldImage) {
                        if (Storage::disk('public')->exists($oldImage)) {
                            Storage::disk('public')->delete($oldImage);
                        }
                    }
                }
            }
        });
    }

    // Relations
    public function equipement(): BelongsTo
    {
        return $this->belongsTo(Equipement::class, 'equipement_id');
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_createur_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsto(user::class, 'user_assignee_id')->where('role', '=', 'technicien');
    }

    // Scopes utiles
    public function scopeOpen($query)
    {
        return $query->where('statut', '!=', 'cloture');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('user_assignee_id', $userId);
    }

    // Méthode pour calculer le score AMDEC
    public function calculateAmdecScore(): int
    {
        return $this->gravite_panne * $this->frequence_occurrence * $this->detectabilite;
    }

    public function pieces(): BelongsToMany
    {
        return $this->belongsToMany(Piece::class, 'interventions_pieces', 'maintenance_corr_id', 'piece_id')
                    ->withPivot('quantite_utilisee')
                    ->using(MaintenanceCorrectivePiece::class)
                    ->withTimestamps();
    }
}
