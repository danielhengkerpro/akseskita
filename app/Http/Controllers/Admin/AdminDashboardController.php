<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Durasi cache statistik dashboard admin (detik).
     */
    private const STATS_TTL = 120;

    /**
     * Dashboard admin: statistik & ringkasan moderasi.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Place::class);

        $stats = Cache::remember('admin.stats', self::STATS_TTL, function (): array {
            // Hitungan per warna & per status dalam satu query agregasi masing-masing.
            $placesByColor = Place::query()
                ->selectRaw('status_color, count(*) as total')
                ->groupBy('status_color')
                ->pluck('total', 'status_color');

            $reportsByStatus = Report::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            return [
                'places' => Place::count(),
                'verified' => Place::where('is_verified', true)->count(),
                'green' => (int) ($placesByColor['green'] ?? 0),
                'yellow' => (int) ($placesByColor['yellow'] ?? 0),
                'red' => (int) ($placesByColor['red'] ?? 0),
                'reports_total' => Report::count(),
                'reports_pending' => (int) ($reportsByStatus[Report::STATUS_PENDING] ?? 0),
                'reports_approved' => (int) ($reportsByStatus[Report::STATUS_APPROVED] ?? 0),
                'reports_rejected' => (int) ($reportsByStatus[Report::STATUS_REJECTED] ?? 0),
                'users' => User::where('role', User::ROLE_USER)->count(),
            ];
        });

        $latestReports = Report::with(['user', 'place'])
            ->latest()
            ->limit(6)
            ->get();

        $categoryBreakdown = Place::query()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('admin.dashboard', compact('stats', 'latestReports', 'categoryBreakdown'));
    }
}
