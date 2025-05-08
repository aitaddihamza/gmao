<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\MaintenancePreventivePiece;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenancePreventive extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'maintenances_preventives';

    protected $fillable = [
        'equipement_id',
        'user_createur_id',
        'user_assignee_id',
        'date_debut',
        'date_fin',
        'description',
        'statut',
        'type_externe',
        'fournisseur',
        'remarques',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    public function pieces(): BelongsToMany
    {
        return $this->belongsToMany(Piece::class, 'interventions_pieces', 'maintenance_prev_id', 'piece_id')
                    ->withPivot('quantite_utilisee')
                    ->using(MaintenancePreventivePiece::class)
                    ->withTimestamps();
    }

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
        return $this->belongsTo(User::class, 'user_assignee_id')
                    ->where('role', '=', 'technicien');
    }
}
