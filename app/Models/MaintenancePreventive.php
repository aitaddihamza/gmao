<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePreventive extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'maintenances_preventives';
    protected $fillable = [
        'equipement_id',
        'user_id',
        'date_planifiee',
        'date_reelle',
        'statut',
        'description',
        'periodicite_jours',
        'remarques',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_planifiee' => 'date',
        'date_reelle' => 'date',
    ];

    /**
     * Get the equipement associated with the preventive maintenance.
     */
    public function equipement()
    {
        return $this->belongsTo(Equipement::class);
    }

    /**
     * Get the user who is responsible for the preventive maintenance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
