<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\Report;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Durasi cache statistik landing page (detik).
     */
    private const STATS_TTL = 300;

    /**
     * Jumlah maksimum tempat yang dikirim ke peta landing page.
     */
    private const MAP_LIMIT = 500;

    /**
     * Landing page animasi & naratif AksesKita.
     */
    public function index(): View
    {
        $featured = Place::query()
            ->where('is_verified', true)
            ->orderByDesc('overall_score')
            ->limit(6)
            ->get();

        $stats = Cache::remember('home.stats', self::STATS_TTL, function (): array {
            // Satu query agregasi untuk hitungan per warna, bukan 3 query terpisah.
            $byColor = Place::query()
                ->selectRaw('status_color, count(*) as total')
                ->groupBy('status_color')
                ->pluck('total', 'status_color');

            return [
                'places' => Place::count(),
                'verified' => Place::where('is_verified', true)->count(),
                'green' => (int) ($byColor['green'] ?? 0),
                'yellow' => (int) ($byColor['yellow'] ?? 0),
                'red' => (int) ($byColor['red'] ?? 0),
                'reports' => Report::count(),
            ];
        });

        $mapPlaces = Place::query()
            ->with('features')
            ->orderByDesc('overall_score')
            ->limit(self::MAP_LIMIT)
            ->get()
            ->map(fn (Place $place) => $place->toMapArray())
            ->values();

        // Titik tengah peta: antara Jakarta & Tangerang.
        $mapCenter = ['lat' => -6.21, 'lng' => 106.78];

        return view('public.home', compact('featured', 'stats', 'mapPlaces', 'mapCenter'));
    }
}
