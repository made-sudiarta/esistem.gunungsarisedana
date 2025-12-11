<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Member;
use Illuminate\Auth\Access\HandlesAuthorization;

class MemberPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        // Super Admin boleh semua aksi
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Kolektor hanya boleh view / viewAny
        if ($user->hasRole('Kolektor') && in_array($ability, ['viewAny', 'view'])) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_member');
    }

    public function view(User $user, Member $member): bool
    {
        return $user->can('view_member');
    }

    public function create(User $user): bool
    {
        return $user->can('create_member');
    }

    public function update(User $user, Member $member): bool
    {
        return $user->can('update_member');
    }

    public function delete(User $user, Member $member): bool
    {
        return $user->can('delete_member');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_member');
    }

    public function forceDelete(User $user, Member $member): bool
    {
        return $user->can('force_delete_member');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_member');
    }

    public function restore(User $user, Member $member): bool
    {
        return $user->can('restore_member');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_member');
    }

    public function replicate(User $user, Member $member): bool
    {
        return $user->can('replicate_member');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_member');
    }
}
