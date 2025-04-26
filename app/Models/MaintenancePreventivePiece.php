<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\Piece;
use App\Models\MaintenancePreventive;

class MaintenancePreventivePiece extends Pivot
{
    protected $table = 'interventions_pieces';
    protected $fillable = [
        'maintenance_prev_id',
        'piece_id',
        'quantite_utilisee'
    ];

    // App\Models\InterventionPiece.php (si vous avez un modèle pivot)
    public function piece()
    {
        return $this->belongsTo(Piece::class, 'piece_id' );
    }

    public function maintenancePreventive()
    {
        return $this->belongsTo(MaintenancePreventive::class, 'maintenance_prev_id' );
    }
}
