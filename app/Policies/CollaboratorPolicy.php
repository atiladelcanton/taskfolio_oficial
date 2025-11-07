<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\{Collaborator, User};

class CollaboratorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('collaborator.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Collaborator $collaborator): bool
    {
        return $user->can('collaborator.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('collaborator.view');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Collaborator $collaborator): bool
    {
        return $user->can('collaborator.view');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Collaborator $collaborator): bool
    {
        return $user->can('collaborator.view');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function attach(User $user, Collaborator $collaborator): bool
    {
        return $user->can('collaborator.view');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function detach(User $user, Collaborator $collaborator): bool
    {
        return $user->can('collaborator.view');
    }
}
