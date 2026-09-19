<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Form laporan fasilitas baru/rusak.
     */
    public function create(): View
    {
        $places = Place::query()->orderBy('name')->get(['id', 'name', 'slug', 'latitude', 'longitude']);

        return view('user.create-report', compact('places'));
    }

    /**
     * Simpan laporan dari warga.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'place_id' => ['nullable', 'exists:places,id'],
            'place_name' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'description' => ['required', 'string', 'min:15', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reports', 'public');
        }

        $report = $request->user()->reports()->create([
            'place_id' => $validated['place_id'] ?? null,
            'place_name' => $validated['place_name'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'description' => $validated['description'],
            'photo_path' => $photoPath,
            'status' => Report::STATUS_PENDING,
        ]);

        return redirect()
            ->route('user.dashboard')
            ->with('status', 'Laporan "'.$report->place_name.'" berhasil dikirim dan menunggu verifikasi admin.');
    }
}
