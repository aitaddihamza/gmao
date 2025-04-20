<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

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
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function maintenancePreventives()
    {
        return $this->belongsToMany(MaintenancePreventive::class, 'interventions_pieces', 'piece_id', 'maintenance_prev_id')
            ->using(MaintenancePreventivePiece::class)
            ->withPivot('quantite_utilisee')
            ->withTimestamps();
    }

    public function maintenanceCorrectives()
    {
        return $this->belongsToMany(MaintenanceCorrective::class, 'interventions_pieces', 'piece_id', 'maintenance_corr_id')
            ->withPivot('quantite_utilisee')
            ->withTimestamps();
    }
}
