<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;

class TicketPolicy
{
    /**
     * Create a new policy instance.
     */

    // les utilisateurs autorisés
    public const ALLOWED_ROLES = [
        'ingenieur',
        'technicien',
    ];


    public function __construct()
    {
        //
    }


    public function create(User $user): bool
    {
        return in_array($user->role, self::ALLOWED_ROLES);
    }


    public function edit(User $user, Ticket $ticket): bool
    {
        return in_array($user->role, self::ALLOWED_ROLES);

    }


    public function delete(User $user, Ticket $ticket): bool
    {
        return in_array($user->role, self::ALLOWED_ROLES);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return in_array($user->role, self::ALLOWED_ROLES);
    }


}
