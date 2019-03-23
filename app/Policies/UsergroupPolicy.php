<?php

namespace App\Policies;

use App\User;
use App\Usergroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class UsergroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */

    /**
     * Determine whether the user can create posts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function store(User $user)
    {
        return $user->checkAccess('usergroups', 'create');
    }
    /**
     * Determine whether the user can update the post.
     *
     * @param  \App\User  $user
     * @param  \App\Post  $post
     * @return mixed
     */
    public function update(User $user, Usergroup $usergroup)
    {
        $check = $user->checkAccess('usergroups', 'update');

        if ($check == 'all') {
            return true;
        } elseif ($check == 'own') {
            return $user->id == $usergroup->user_id;
        }

        return $check;
    }

    /**
     * Determine whether the user can delete the Usergroup.
     *
     * @param  \App\User  $user
     * @param  \App\Usergroup  $Usergroup
     * @return mixed
     */
    public function destroy(User $user, Usergroup $usergroup)
    {
        $check = $user->checkAccess('usergroups', 'delete');

        if ($check == 'all') {
            return true;
        } elseif ($check == 'own') {
            return $user->id == $usergroup->user_id;
        }

        return $check;
    }

    public function access(User $user)
    {
        return $user->checkAccess('usergroups', 'access');
    }
}
