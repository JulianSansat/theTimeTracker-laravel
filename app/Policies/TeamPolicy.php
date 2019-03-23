<?php

namespace App\Policies;

use App\User;
use App\Team;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the Team.
     *
     * @param  \App\User  $user
     * @param  \App\Team  $team
     * @return mixed
     */

    /**
     * Determine whether the user can create Teams.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function store(User $user)
    {
        return $user->checkAccess('teams', 'create');
    }
    /**
     * Determine whether the user can update the Team.
     *
     * @param  \App\User  $user
     * @param  \App\Team  $team
     * @return mixed
     */
    public function update(User $user, Team $team)
    {
        $check = $user->checkAccess('teams', 'update');

        if ($check == 'all') {
            return true;
        } elseif ($check == 'own') {
            return $user->id == $team->user_id;
        }

        return $check;
    }

    /**
     * Determine whether the user can delete the Team.
     *
     * @param  \App\User  $user
     * @param  \App\Team  $team
     * @return mixed
     */
    public function destroy(User $user, Team $team)
    {
        $check = $user->checkAccess('teams', 'delete');

        if ($check == 'all') {
            return true;
        } elseif ($check == 'own') {
            return $user->id == $team->user_id;
        }

        return $check;
    }

    public function access(User $user)
    {
        return $user->checkAccess('teams', 'access');
    }
}
