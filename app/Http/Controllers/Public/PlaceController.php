<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Place;
use Illuminate\View\View;

class PlaceController extends Controller
{
    /**
     * Detail tempat: skor, checklist fitur, dan peta mini.
     */
    public function show(Place $place): View
    {
        $place->load(['features', 'creator']);

        $related = Place::query()
            ->where('id', '!=', $place->id)
            ->where('category', $place->category)
            ->orderByDesc('overall_score')
            ->limit(3)
            ->get();

        if ($related->isEmpty()) {
            $related = Place::query()
                ->where('id', '!=', $place->id)
                ->orderByDesc('overall_score')
                ->limit(3)
                ->get();
        }

        $checklist = $place->features?->checklist() ?? [];

        return view('public.place-detail', compact('place', 'related', 'checklist'));
    }
}
