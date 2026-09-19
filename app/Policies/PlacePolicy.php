<?php

namespace App\Policies;

use App\Models\Place;
use App\Models\User;

class PlacePolicy
{
    /**
     * Melihat daftar tempat (panel admin) — hanya admin.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Melihat detail tempat — publik (semua user yang terautentikasi).
     */
    public function view(User $user, Place $place): bool
    {
        return true;
    }

    /**
     * Membuat tempat baru — hanya admin.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Memperbarui tempat — hanya admin.
     */
    public function update(User $user, Place $place): bool
    {
        return $user->isAdmin();
    }

    /**
     * Menghapus tempat — hanya admin.
     */
    public function delete(User $user, Place $place): bool
    {
        return $user->isAdmin();
    }
}
