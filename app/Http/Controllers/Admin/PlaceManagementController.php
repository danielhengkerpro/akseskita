<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessibilityFeature;
use App\Models\Place;
use App\Services\PlaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlaceManagementController extends Controller
{
    public function __construct(private readonly PlaceService $places)
    {
    }

    /**
     * Daftar semua tempat (admin).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Place::class);

        $validated = $request->validate([
            'category' => ['nullable', 'string', Rule::in(array_merge(['all'], array_keys(Place::CATEGORIES)))],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $places = Place::query()
            ->with('features')
            ->when(! empty($validated['category']) && $validated['category'] !== 'all',
                fn ($q) => $q->where('category', $validated['category']))
            ->when(! empty($validated['search']),
                fn ($q) => $q->where('name', 'like', '%'.$validated['search'].'%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Place::CATEGORIES;

        return view('admin.places-index', compact('places', 'categories'));
    }

    /**
     * Form tambah tempat baru.
     */
    public function create(): View
    {
        $this->authorize('create', Place::class);

        $place = new Place(['status_color' => 'yellow', 'overall_score' => 0, 'is_verified' => true]);
        $features = new AccessibilityFeature;

        return view('admin.places-form', [
            'place' => $place,
            'features' => $features,
            'categories' => Place::CATEGORIES,
            'action' => route('admin.places.store'),
            'method' => 'POST',
        ]);
    }

    /**
     * Simpan tempat baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Place::class);

        $validated = $this->validatePlace($request);

        $place = $this->places->createPlace(
            $validated['place'],
            $validated['features'],
            $request->user()->id,
        );

        return redirect()
            ->route('admin.places.index')
            ->with('status', 'Tempat "'.$place->name.'" berhasil ditambahkan.');
    }

    /**
     * Form edit tempat.
     */
    public function edit(Place $place): View
    {
        $this->authorize('update', $place);

        $place->load('features');
        $features = $place->features ?? new AccessibilityFeature(['place_id' => $place->id]);

        return view('admin.places-form', [
            'place' => $place,
            'features' => $features,
            'categories' => Place::CATEGORIES,
            'action' => route('admin.places.update', $place),
            'method' => 'PUT',
        ]);
    }

    /**
     * Update data tempat + fitur & hitung ulang skor.
     */
    public function update(Request $request, Place $place): RedirectResponse
    {
        $this->authorize('update', $place);

        $validated = $this->validatePlace($request, $place);

        $this->places->updatePlace($place, $validated['place'], $validated['features']);

        return redirect()
            ->route('admin.places.index')
            ->with('status', 'Tempat "'.$place->name.'" berhasil diperbarui.');
    }

    /**
     * Hapus tempat.
     */
    public function destroy(Place $place): RedirectResponse
    {
        $this->authorize('delete', $place);

        $name = $place->name;
        $place->delete();

        return redirect()
            ->route('admin.places.index')
            ->with('status', 'Tempat "'.$name.'" berhasil dihapus.');
    }

    /**
     * Validasi input tempat + fitur aksesibilitas.
     *
     * @return array{place: array<string, mixed>, features: array<string, mixed>}
     */
    private function validatePlace(Request $request, ?Place $place = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(array_keys(Place::CATEGORIES))],
            'address' => ['required', 'string', 'max:1000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'is_verified' => ['nullable', 'boolean'],
            'has_ramp' => ['nullable', 'boolean'],
            'has_lift' => ['nullable', 'boolean'],
            'has_accessible_toilet' => ['nullable', 'boolean'],
            'has_guiding_block' => ['nullable', 'boolean'],
            'wide_door' => ['nullable', 'boolean'],
            'braille_lift' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $placeData = [
            'name' => $validated['name'],
            'category' => $validated['category'],
            'address' => $validated['address'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'is_verified' => $request->boolean('is_verified'),
        ];

        $featureData = [];
        foreach (AccessibilityFeature::criteria() as $criterion) {
            $featureData[$criterion['key']] = $request->boolean($criterion['key']);
        }
        $featureData['notes'] = $validated['notes'] ?? null;

        return ['place' => $placeData, 'features' => $featureData];
    }

    /**
     * Setujui dan verifikasi tempat yang diajukan oleh pengguna.
     */
    public function verify(Request $request, Place $place): RedirectResponse
    {
        $place->update(['is_verified' => true]);

        return back()->with('status', 'Tempat "'.$place->name.'" berhasil diverifikasi dan sekarang aktif di peta publik!');
    }

    /**
     * Tolak atau batalkan pengajuan tempat.
     */
    public function reject(Request $request, Place $place): RedirectResponse
    {
        $name = $place->name;
        $place->delete();

        return back()->with('status', 'Pengajuan tempat "'.$name.'" telah ditolak dan dihapus.');
    }
}
