<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class MaintenancePreventive extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'maintenances_preventives';

    protected $fillable = [
        'equipement_id',
        'user_id',
        'date_planifiee',
        'date_reelle',
        'description',
        'statut',
        'periodicite_jours',
        'remarques',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date_planifiee' => 'date',
        'date_reelle' => 'date',
    ];

    public function pieces()
    {
        return $this->belongsToMany(Piece::class, 'interventions_pieces', 'maintenance_prev_id', 'piece_id')
            ->using(MaintenancePreventivePiece::class)
            ->withPivot('quantite_utilisee')
            ->withTimestamps();
    }

    public function equipement()
    {
        return $this->belongsTo(Equipement::class, 'equipement_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
