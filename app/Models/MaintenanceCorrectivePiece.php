<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Piece;
use App\Models\Ticket;

class MaintenanceCorrectivePiece extends Pivot
{
    protected $table = 'interventions_pieces';
    protected $fillable = [
        'maintenance_corr_id',
        'piece_id',
        'quantite_utilisee'
    ];

    public function piece()
    {
        return $this->belongsTo(Piece::class, 'piece_id');
    }

    public function maintenanceCorrective()
    {
        return $this->belongsTo(Ticket::class, 'maintenance_corr_id');
    }
};
