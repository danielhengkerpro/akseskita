<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Services\PlaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlaceController extends Controller
{
    public function __construct(private readonly PlaceService $places)
    {
    }

    /**
     * Tampilkan formulir pengajuan tempat baru oleh warga/disabilitas.
     */
    public function create(): View
    {
        $categories = Place::CATEGORIES;

        return view('user.create-place', compact('categories'));
    }

    /**
     * Simpan pengajuan tempat baru (status awal: is_verified = false / menunggu verifikasi admin).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(array_keys(Place::CATEGORIES))],
            'address' => ['required', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'has_ramp' => ['nullable', 'boolean'],
            'has_lift' => ['nullable', 'boolean'],
            'has_accessible_toilet' => ['nullable', 'boolean'],
            'has_guiding_block' => ['nullable', 'boolean'],
            'wide_door' => ['nullable', 'boolean'],
            'braille_lift' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Place::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $placeData = [
            'name' => $validated['name'],
            'slug' => $slug,
            'category' => $validated['category'],
            'address' => $validated['address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'is_verified' => false, // Perlu verifikasi admin sebelum tampil di peta publik
        ];

        $featureData = [
            'has_ramp' => $request->boolean('has_ramp'),
            'has_lift' => $request->boolean('has_lift'),
            'has_accessible_toilet' => $request->boolean('has_accessible_toilet'),
            'has_guiding_block' => $request->boolean('has_guiding_block'),
            'wide_door' => $request->boolean('wide_door'),
            'braille_lift' => $request->boolean('braille_lift'),
            'notes' => $validated['notes'] ?? null,
        ];

        $place = $this->places->createPlace($placeData, $featureData, $request->user()->id);

        return redirect()
            ->route('user.dashboard')
            ->with('status', 'Tempat baru "'.$place->name.'" berhasil diajukan! Admin verifikator akan meninjau data sebelum diterbitkan ke peta publik.');
    }
}
