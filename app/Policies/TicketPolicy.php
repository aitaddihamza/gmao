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
    // allowed users = the one who created the ticket and the assigned user

    public function __construct()
    {
        //
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['ingenieur', 'technicien']);
    }


    public function edit(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_createur_id || $user->id === $ticket->user_assignee_id;
    }


    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_createur_id || $user->id === $ticket->user_assignee_id;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_createur_id || $user->id === $ticket->user_assignee_id;
    }


}
