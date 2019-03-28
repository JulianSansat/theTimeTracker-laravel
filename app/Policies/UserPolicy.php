<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the User.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return mixed
     */

    /**
     * Determine whether the user can create Users.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function store(User $user)
    {
        return $user->checkAccess('users', 'create');
    }
    /**
     * Determine whether the user can update the User.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        $check = $user->checkAccess('users', 'update');

        if ($check == 'all') {
            return true;
        } elseif ($check == 'own') {
            return $user->id == $user->user_id;
        }

        return $check;
    }

    /**
     * Determine whether the user can delete the User.
     *
     * @param  \App\User  $user
     * @param  \App\User  $user
     * @return mixed
     */
    public function destroy(User $user)
    {
        $check = $user->checkAccess('users', 'delete');

        if ($check == 'all') {
            return true;
        } elseif ($check == 'own') {
            return $user->id == $user->user_id;
        }

        return $check;
    }

    public function access(User $user)
    {
        return $user->checkAccess('users', 'access');
    }
}
