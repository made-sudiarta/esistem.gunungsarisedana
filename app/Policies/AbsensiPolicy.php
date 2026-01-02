<?php

namespace App\Policies;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AbsensiPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // atau pakai permission
    }

    public function view(User $user, Absensi $absensi): bool
    {
        return $user->id === $absensi->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Absensi $absensi): bool
    {
        return $user->id === $absensi->user_id;
    }

    public function delete(User $user, Absensi $absensi): bool
    {
        return false; // absensi sebaiknya tidak dihapus user
    }
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

}
