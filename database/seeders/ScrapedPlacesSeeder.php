<?php

namespace Database\Seeders;

use App\Models\AccessibilityFeature;
use App\Models\Place;
use App\Models\User;
use App\Services\AccessibilityCalculator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ScrapedPlacesSeeder extends Seeder
{
    /**
     * Import hasil scraping tempat ke database.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/scraped_places.json');

        if (!File::exists($jsonPath)) {
            $this->command?->error("File $jsonPath tidak ditemukan.");
            return;
        }

        $items = json_decode(File::get($jsonPath), true);

        if (!is_array($items)) {
            $this->command?->error("Format JSON pada $jsonPath tidak valid.");
            return;
        }

        $calculator = app(AccessibilityCalculator::class);
        $admin = User::where('role', User::ROLE_ADMIN)->first() ?? User::first();
        $importedCount = 0;

        foreach ($items as $data) {
            $features = $data['features'] ?? [];
            unset($data['features']);

            $name = trim($data['name']);
            $address = trim($data['address']);

            // Cek apakah tempat sudah pernah ada berdasarkan nama dan alamat/koordinat
            $place = Place::where('name', $name)
                ->where(function ($query) use ($address, $data) {
                    $query->where('address', $address)
                          ->orWhere(function ($q) use ($data) {
                              $q->whereBetween('latitude', [$data['latitude'] - 0.0001, $data['latitude'] + 0.0001])
                                ->whereBetween('longitude', [$data['longitude'] - 0.0001, $data['longitude'] + 0.0001]);
                          });
                })
                ->first();

            if (!$place) {
                $slug = Place::generateUniqueSlug($name);
                $place = new Place();
                $place->slug = $slug;
            }

            $place->name = $name;
            $place->category = $data['category'] ?? 'fasilitas_publik';
            $place->address = $address;
            $place->latitude = $data['latitude'];
            $place->longitude = $data['longitude'];
            $place->is_verified = $data['is_verified'] ?? true;
            $place->created_by = $admin?->id;
            $place->save();

            // Simpan fitur aksesibilitas
            $featureModel = AccessibilityFeature::updateOrCreate(
                ['place_id' => $place->id],
                [
                    'has_ramp' => (bool) ($features['has_ramp'] ?? false),
                    'has_lift' => (bool) ($features['has_lift'] ?? false),
                    'has_accessible_toilet' => (bool) ($features['has_accessible_toilet'] ?? false),
                    'has_guiding_block' => (bool) ($features['has_guiding_block'] ?? false),
                    'wide_door' => (bool) ($features['wide_door'] ?? false),
                    'braille_lift' => (bool) ($features['braille_lift'] ?? false),
                    'notes' => $features['notes'] ?? 'Data diimpor dari OpenStreetMap.',
                ]
            );

            // Hitung skor & status color
            $place->overall_score = $calculator->score($featureModel);
            $place->status_color = $calculator->color($place->overall_score);
            $place->saveQuietly();

            $importedCount++;
        }

        $this->command?->info("Berhasil mengimpor {$importedCount} tempat dari OpenStreetMap ke database!");
    }
}
