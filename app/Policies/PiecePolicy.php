<?php

namespace App\Policies;

use App\Models\Piece;
use App\Models\User;

class PiecePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $user): bool
    {
        return $user->role === 'majeur' || $user->role == 'ingenieur';
    }


    public function edit(User $user, Piece $piece): bool
    {
        return $user->role === 'majeur' || $user->role == 'ingenieur';
    }


    public function delete(User $user, Piece $piece): bool
    {
        return $user->role === 'majeur' || $user->role == 'ingenieur';
    }


    public function update(User $user, Piece $piece): bool
    {
        return $user->role === 'majeur' || $user->role == 'ingenieur';
    }
}
