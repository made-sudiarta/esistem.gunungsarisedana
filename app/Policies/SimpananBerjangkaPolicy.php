<?php

namespace App\Policies;

use App\Models\SimpananBerjangka;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SimpananBerjangkaPolicy
{
    use HandlesAuthorization;

    /**
     * Mengizinkan Super Admin otomatis
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('super_admin')) {
            return true; // Super Admin bisa melakukan semua aksi
        }
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_simpanan_berjangka');
    }

    public function view(User $user, SimpananBerjangka $simpananBerjangka): bool
    {
        return $user->hasPermissionTo('view_simpanan_berjangka');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_simpanan_berjangka');
    }

    public function update(User $user, SimpananBerjangka $simpananBerjangka): bool
    {
        return $user->hasPermissionTo('update_simpanan_berjangka');
    }

    public function delete(User $user, SimpananBerjangka $simpananBerjangka): bool
    {
        return $user->hasPermissionTo('delete_simpanan_berjangka');
    }

    public function restore(User $user, SimpananBerjangka $simpananBerjangka): bool
    {
        return $user->hasPermissionTo('restore_simpanan_berjangka');
    }

    public function forceDelete(User $user, SimpananBerjangka $simpananBerjangka): bool
    {
        return $user->hasPermissionTo('force_delete_simpanan_berjangka');
    }
}
