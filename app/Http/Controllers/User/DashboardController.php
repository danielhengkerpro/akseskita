<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard user: ringkasan kontribusi & riwayat laporan.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $reports = $user->reports()
            ->with('place')
            ->latest()
            ->paginate(10, ['*'], 'laporan');

        // Satu query agregasi untuk seluruh ringkasan laporan.
        $reportRow = $user->reports()
            ->selectRaw(
                'count(*) as total,
                 sum(case when status = ? then 1 else 0 end) as pending,
                 sum(case when status = ? then 1 else 0 end) as approved,
                 sum(case when status = ? then 1 else 0 end) as rejected',
                [Report::STATUS_PENDING, Report::STATUS_APPROVED, Report::STATUS_REJECTED]
            )
            ->first();

        $reportSummary = [
            'total' => (int) ($reportRow->total ?? 0),
            'pending' => (int) ($reportRow->pending ?? 0),
            'approved' => (int) ($reportRow->approved ?? 0),
            'rejected' => (int) ($reportRow->rejected ?? 0),
        ];

        // Ringkasan tempat yang diajukan oleh user.
        $userPlaces = $user->places()->latest()->get();
        $placeSummary = [
            'total' => $userPlaces->count(),
            'pending' => $userPlaces->where('is_verified', false)->count(),
            'verified' => $userPlaces->where('is_verified', true)->count(),
        ];

        $greenPlaces = Place::where('status_color', 'green')->count();

        // Info disabilitas untuk personalisasi dashboard
        $disabilityInfo = $user->getDisabilityInfo();

        return view('user.dashboard', compact(
            'user',
            'reports',
            'reportSummary',
            'placeSummary',
            'userPlaces',
            'greenPlaces',
            'disabilityInfo'
        ));
    }

    /**
     * Perbarui preferensi aksesibilitas pengguna.
     */
    public function updatePreferences(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'disability_type' => ['nullable', 'in:' . implode(',', array_keys(User::DISABILITY_TYPES))],
            'account_type' => ['nullable', 'in:disability,contributor'],
        ]);

        $user->update([
            'account_type' => $validated['account_type'] ?? $user->account_type,
            'disability_type' => $validated['disability_type'] ?? $user->disability_type,
        ]);

        return redirect()->route('user.dashboard')
            ->with('status', 'Preferensi aksesibilitas berhasil diperbarui.');
    }
}
