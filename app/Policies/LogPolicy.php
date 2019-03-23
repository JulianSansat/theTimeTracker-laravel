<?php

namespace App\Policies;

use App\User;
use App\Log;
use Illuminate\Auth\Access\HandlesAuthorization;

class LogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the Log.
     *
     * @param  \App\User  $user
     * @param  \App\Log  $log
     * @return mixed
     */

    /**
     * Determine whether the user can create Logs.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function store(User $user)
    {
        return $user->checkAccess('logs', 'create');
    }

    /**
     * Determine whether the user can delete the Log.
     *
     * @param  \App\User  $user
     * @param  \App\Log  $log
     * @return mixed
     */
    public function destroy(User $user, Log $log)
    {
        $check = $user->checkAccess('logs', 'delete');

        if ($check == 'all') {
            return true;
        } elseif ($check == 'own') {
            return $user->id == $log->user_id;
        }

        return $check;
    }

    public function access(User $user)
    {
        return $user->checkAccess('logs', 'access');
    }
}
