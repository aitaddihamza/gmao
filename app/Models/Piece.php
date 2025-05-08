<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\MaintenancePreventivePiece;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\MaintenanceCorrectivePiece;
use Illuminate\Support\Facades\Storage;

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

    protected static function booted()
    {
        static::saving(function ($piece) {
            // Si le manuel_path a changé et qu'il y avait un ancien manuel
            if ($piece->isDirty('manuel_path') && $piece->getOriginal('manuel_path')) {
                $oldPath = $piece->getOriginal('manuel_path');
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        });

        static::deleting(function ($piece) {
            // Supprimer le manuel lors de la suppression de la pièce
            if ($piece->manuel_path && Storage::disk('public')->exists($piece->manuel_path)) {
                Storage::disk('public')->delete($piece->manuel_path);
            }
        });
    }

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
