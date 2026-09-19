<?php

namespace App\Services;

use App\Models\AccessibilityFeature;
use App\Models\Place;
use Illuminate\Support\Facades\DB;

class PlaceService
{
    public function __construct(private readonly AccessibilityCalculator $calculator)
    {
    }

    /**
     * Simpan tempat baru beserta fitur aksesibilitasnya dan skor
     * terhitung, dalam satu transaksi atomik.
     *
     * @param  array<string, mixed>  $placeData
     * @param  array<string, mixed>  $featureData
     */
    public function createPlace(array $placeData, array $featureData, int $createdBy): Place
    {
        return DB::transaction(function () use ($placeData, $featureData, $createdBy) {
            $place = Place::create(array_merge($placeData, ['created_by' => $createdBy]));

            $feature = AccessibilityFeature::create(
                array_merge($featureData, ['place_id' => $place->id])
            );

            $this->syncScore($place, $feature);

            return $place;
        });
    }

    /**
     * Perbarui tempat + fitur (upsert) dan skor terhitung, dalam satu
     * transaksi atomik.
     *
     * @param  array<string, mixed>  $placeData
     * @param  array<string, mixed>  $featureData
     */
    public function updatePlace(Place $place, array $placeData, array $featureData): Place
    {
        return DB::transaction(function () use ($place, $placeData, $featureData) {
            $place->update($placeData);

            $feature = AccessibilityFeature::updateOrCreate(
                ['place_id' => $place->id],
                $featureData
            );

            $this->syncScore($place, $feature);

            return $place;
        });
    }

    /**
     * Hitung ulang skor & warna dari fitur, lalu simpan tanpa memicu
     * event model (mencegah loop / observer berulang).
     */
    public function syncScore(Place $place, AccessibilityFeature $features): void
    {
        $place->overall_score = $this->calculator->score($features);
        $place->status_color = $this->calculator->color($place->overall_score);
        $place->saveQuietly();
    }
}
