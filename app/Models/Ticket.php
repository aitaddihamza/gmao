<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'date_creation' => 'datetime',
        'date_intervention' => 'datetime',
        'date_resolution' => 'datetime',
        'chemin_image' => 'array',
    ];

    // Relations
    public function equipement(): BelongsTo
    {
        return $this->belongsTo(Equipement::class);
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
