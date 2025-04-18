<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'gravite_panne',
        'frequence_occurrence',
        'detectabilite',
        'date_creation',
        'date_attribution',
        'date_cloture',
    ];

    protected $casts = [
        'date_creation' => 'datetime',
        'date_attribution' => 'datetime',
        'date_cloture' => 'datetime',
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
        return $this->belongsTo(User::class, 'user_assignee_id');
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
}
