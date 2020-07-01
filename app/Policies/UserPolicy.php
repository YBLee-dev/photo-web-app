<?php

namespace App\Policies;

use App\Users\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can edit the model.
     *
     * @param  \App\Users\User $user
     * @param \App\Users\User  $user_page
     *
     * @return mixed
     */
    public function edit(User $user, User $user_page)
    {
        return $user->id == $user_page->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Users\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Users\User $user
     *
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view list of users.
     *
     * @param  \App\Users\User $user
     *
     * @return mixed
     */
    public function only_admin(User $user)
    {
        return $user->isAdmin();
    }
}
