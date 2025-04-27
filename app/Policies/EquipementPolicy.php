<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Equipement;


class EquipementPolicy
{
    /**
     * Ce fichier définit la politique d'autorisation pour les équipements.
     * Il contrôle qui peut effectuer différentes actions sur les équipements.
     */
    public function __construct()
    {
        // Le constructeur est vide car aucune initialisation n'est nécessaire
    }



    /**
     * Détermine si l'utilisateur peut modifier un équipement
     * Seuls les responsables peuvent modifier
     */
    public function edit(User $user, Equipement $equipement): bool
    {
        return $user->role === 'responsable';
    }

    /**
     * Détermine si l'utilisateur peut supprimer un équipement
     * Seuls les responsables peuvent supprimer
     */
    public function delete(User $user, Equipement $equipement): bool
    {
        return $user->role === 'responsable';
    }
    
    /**
     * Détermine si l'utilisateur peut créer un équipement
     * Seuls les responsables peuvent créer
     */
    public function create(User $user): bool
    {
        return $user->role === 'responsable';
    }

    public function update(User $user, Equipement $equipement): bool
    {
        return $user->role === 'responsable';
    }
}
