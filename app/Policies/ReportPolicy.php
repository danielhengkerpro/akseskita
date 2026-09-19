<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    /**
     * Melihat daftar laporan (moderasi) — hanya admin.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Melihat satu laporan — admin atau pemilik laporan.
     */
    public function view(User $user, Report $report): bool
    {
        return $user->isAdmin() || $report->user_id === $user->id;
    }

    /**
     * Membuat laporan baru — hanya user biasa (kontributor).
     */
    public function create(User $user): bool
    {
        return $user->isUser();
    }

    /**
     * Memoderasi (approve/reject) laporan — hanya admin DAN
     * laporan masih berstatus pending (cegah review ulang atas
     * keputusan yang sudah final).
     */
    public function moderate(User $user, Report $report): bool
    {
        return $user->isAdmin() && $report->isPending();
    }
}
