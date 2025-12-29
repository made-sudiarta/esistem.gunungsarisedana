<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SimpananBerjangka;
use Illuminate\Auth\Access\HandlesAuthorization;

class SimpananBerjangkaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_simpanan::berjangka');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SimpananBerjangka $simpananBerjangka): bool
    {
        return $user->can('view_simpanan::berjangka');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_simpanan::berjangka');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SimpananBerjangka $simpananBerjangka): bool
    {
        return $user->can('update_simpanan::berjangka');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SimpananBerjangka $simpananBerjangka): bool
    {
        return $user->can('delete_simpanan::berjangka');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_simpanan::berjangka');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, SimpananBerjangka $simpananBerjangka): bool
    {
        return $user->can('force_delete_simpanan::berjangka');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_simpanan::berjangka');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, SimpananBerjangka $simpananBerjangka): bool
    {
        return $user->can('restore_simpanan::berjangka');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_simpanan::berjangka');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, SimpananBerjangka $simpananBerjangka): bool
    {
        return $user->can('replicate_simpanan::berjangka');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_simpanan::berjangka');
    }
}
