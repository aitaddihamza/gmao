<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MaintenancePreventive;

class MaintenancePreventivePolicy
{
    /**
     * Create a new policy instance.
     */

    public const ALLOWED_ROLES = [
        'engineer',
    ];

    public function __construct()
    {
        //
    }
    public function create(User $user): bool
    {
        return in_array($user->role, self::ALLOWED_ROLES);
    }


    public function edit(User $user, MaintenancePreventive $maintenancePreventive): bool
    {
        return $user->id === $maintenancePreventive->user_id || in_array($user->role, self::ALLOWED_ROLES);

    }


    public function delete(User $user, MaintenancePreventive $maintenancePreventive): bool
    {
        // seul le créateur
        return $user->id === $maintenancePreventive->user_assignee_id;
    }

    public function update(User $user, MaintenancePreventive $maintenancePreventive): bool
    {
        return $user->id === $maintenancePreventive->user_id || in_array($user->role, self::ALLOWED_ROLES);
    }


}
