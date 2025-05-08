<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\MaintenancePreventivePiece;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Models\Equipement;
use App\Models\User;

class MaintenancePreventive extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'maintenances_preventives';

    protected $fillable = [
        'equipement_id',
        'date_debut',
        'date_fin',
        'statut',
        'description',
        'remarques',
        'rapport_path',
        'type_externe',
        'fournisseur',
        'rapport_type',
        'actions_realisees',
        'user_createur_id',
        'user_assignee_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($maintenance) {
            // Si le rapport_path a changé et qu'il y avait un ancien rapport
            if ($maintenance->isDirty('rapport_path') && $maintenance->getOriginal('rapport_path')) {
                $oldPath = $maintenance->getOriginal('rapport_path');
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        });

        static::deleting(function ($maintenance) {
            // Supprimer le rapport lors de la suppression de la maintenance
            if ($maintenance->rapport_path && Storage::disk('public')->exists($maintenance->rapport_path)) {
                Storage::disk('public')->delete($maintenance->rapport_path);
            }
        });
    }

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
