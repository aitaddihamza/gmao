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
        return $user->role === 'majeur' || $user->role == 'engineer';
    }


    public function edit(User $user, Piece $piece): bool
    {
        return $user->role === 'majeur' || $user->role == 'engineer';
    }


    public function delete(User $user, Piece $piece): bool
    {
        return $user->role === 'majeur' || $user->role == 'engineer';
    }


    public function update(User $user, Piece $piece): bool
    {
        return $user->role === 'majeur' || $user->role == 'engineer';
    }
}
