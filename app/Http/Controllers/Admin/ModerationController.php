<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModerationController extends Controller
{
    /**
     * Daftar laporan warga untuk dimoderasi.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Report::class);

        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:all,pending,approved,rejected'],
        ]);

        $status = $validated['status'] ?? 'pending';

        $reports = Report::with(['user', 'place', 'reviewer'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Satu query agregasi untuk seluruh tab hitungan.
        $byStatus = Report::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'all' => (int) $byStatus->sum(),
            'pending' => (int) ($byStatus[Report::STATUS_PENDING] ?? 0),
            'approved' => (int) ($byStatus[Report::STATUS_APPROVED] ?? 0),
            'rejected' => (int) ($byStatus[Report::STATUS_REJECTED] ?? 0),
        ];

        return view('admin.pending-reports', compact('reports', 'status', 'counts'));
    }

    /**
     * Setujui laporan warga.
     */
    public function approve(Request $request, Report $report): RedirectResponse
    {
        $this->authorize('moderate', $report);

        $report->update([
            'status' => Report::STATUS_APPROVED,
            'reviewed_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Laporan "'.$report->place_name.'" telah disetujui.');
    }

    /**
     * Tolak laporan warga.
     */
    public function reject(Request $request, Report $report): RedirectResponse
    {
        $this->authorize('moderate', $report);

        $report->update([
            'status' => Report::STATUS_REJECTED,
            'reviewed_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Laporan "'.$report->place_name.'" telah ditolak.');
    }
}
