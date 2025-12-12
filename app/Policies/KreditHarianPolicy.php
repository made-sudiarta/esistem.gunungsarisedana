<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KreditHarian;
use Illuminate\Auth\Access\HandlesAuthorization;

class KreditHarianPolicy
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
        return $user->can('view_any_kredit_harian');
    }

    public function view(User $user, KreditHarian $kreditHarian): bool
    {
        return $user->can('view_kredit_harian');
    }

    public function create(User $user): bool
    {
        return $user->can('create_kredit_harian');
    }

    public function update(User $user, KreditHarian $kreditHarian): bool
    {
        return $user->can('update_kredit_harian');
    }

    public function delete(User $user, KreditHarian $kreditHarian): bool
    {
        return $user->can('delete_kredit_harian');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_kredit_harian');
    }

    public function forceDelete(User $user, KreditHarian $kreditHarian): bool
    {
        return $user->can('force_delete_kredit_harian');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_kredit_harian');
    }

    public function restore(User $user, KreditHarian $kreditHarian): bool
    {
        return $user->can('restore_kredit_harian');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_kredit_harian');
    }

    public function replicate(User $user, KreditHarian $kreditHarian): bool
    {
        return $user->can('replicate_kredit_harian');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_kredit_harian');
    }
}
