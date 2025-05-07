<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\MaintenancePreventivePiece;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\MaintenanceCorrectivePiece;

class Piece extends Model
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'designation',
        'reference',
        'quantite_stock',
        'fournisseur',
        'prix_unitaire',
        'manuel_path',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function maintenancePreventives(): BelongsToMany
    {
        return $this->belongsToMany(MaintenancePreventive::class, 'interventions_pieces', 'piece_id', 'maintenance_prev_id')
            ->using(MaintenancePreventivePiece::class)
            ->withPivot('quantite_utilisee')
            ->withTimestamps();
    }

    public function maintenanceCorrectives(): BelongsToMany
    {
        return $this->belongsToMany(MaintenanceCorrectivePiece::class, 'interventions_pieces', 'piece_id', 'maintenance_corr_id')
            ->withPivot('quantite_utilisee')
            ->withTimestamps();
    }
}
