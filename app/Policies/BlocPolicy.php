<?php

namespace App\Policies;

use App\Models\Bloc;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BlocPolicy
{
    /**
     * Determine whether the user can view any models.
     */



    /**
     * Determine whether the user can view the model.
     */

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'majeur' || $user->role === 'ingenieur' || $user->role === 'chef';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bloc $bloc): bool
    {
        return $user->role === 'majeur' || $user->role === 'ingenieur' || $user->role === 'chef';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bloc $bloc): bool
    {
        return $user->role === 'majeur' || $user->role === 'ingenieur' || $user->role === 'chef';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bloc $bloc): bool
    {
        return $user->role === 'majeur' || $user->role === 'ingenieur' || $user->role === 'chef';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bloc $bloc): bool
    {
        return $user->role === 'majeur' || $user->role === 'ingenieur';
    }
}
