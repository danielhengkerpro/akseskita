<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    /**
     * Halaman peta fullscreen interaktif.
     */
    public function index(): View
    {
        $mapCenter = ['lat' => -6.21, 'lng' => 106.78];
        $categories = Place::CATEGORIES;

        return view('public.map', compact('mapCenter', 'categories'));
    }

    /**
     * Endpoint JSON data tempat untuk konsumsi Leaflet + filter.
     */
    public function getPlacesJson(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['nullable', 'string', 'in:all,'.implode(',', array_keys(Place::CATEGORIES))],
            'status' => ['nullable', 'string', 'in:all,green,yellow,red'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = Place::query();

        if (! empty($validated['category']) && $validated['category'] !== 'all') {
            $query->where('category', $validated['category']);
        }

        if (! empty($validated['status']) && $validated['status'] !== 'all') {
            $query->where('status_color', $validated['status']);
        }

        if (! empty($validated['search'])) {
            $search = '%'.$validated['search'].'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('address', 'like', $search);
            });
        }

        $places = $query
            ->with('features')
            ->orderByDesc('overall_score')
            ->limit(1000)
            ->get()
            ->map(fn (Place $place) => $place->toMapArray())
            ->values();

        return response()->json([
            'count' => $places->count(),
            'places' => $places,
        ]);
    }
}
