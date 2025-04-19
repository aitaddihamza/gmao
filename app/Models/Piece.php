<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\MaintenancePreventive;
use App\Models\MaintenanceCorrective;

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
        return $this->belongsToMany(MaintenancePreventive::class, 'interventions_pieces')
            ->withPivot('quantite_utilisee')
            ->withTimestamps();
    }

    public function maintenanceCorrectives()
    {
        return $this->belongsToMany(MaintenanceCorrective::class, 'interventions_pieces')
            ->withPivot('quantite_utilisee')
            ->withTimestamps();
    }

}
