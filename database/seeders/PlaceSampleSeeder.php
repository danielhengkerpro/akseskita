<?php

namespace Database\Seeders;

use App\Models\AccessibilityFeature;
use App\Models\Place;
use App\Models\User;
use App\Services\AccessibilityCalculator;
use Illuminate\Database\Seeder;

class PlaceSampleSeeder extends Seeder
{
    /**
     * Seed 20 tempat sampel di Jakarta & Tangerang beserta
     * data fitur aksesibilitas dan skor kelayakannya.
     */
    public function run(): void
    {
        $calculator = app(AccessibilityCalculator::class);
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        foreach ($this->places() as $data) {
            $features = $data['features'];
            unset($data['features']);

            /** @var Place $place */
            $place = Place::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['created_by' => $admin?->id])
            );

            $featureModel = AccessibilityFeature::updateOrCreate(
                ['place_id' => $place->id],
                array_merge($features, ['place_id' => $place->id])
            );

            $place->overall_score = $calculator->score($featureModel);
            $place->status_color = $calculator->color($place->overall_score);
            $place->saveQuietly();
        }
    }

    /**
     * Data 20 tempat sampel.
     *
     * @return array<int, array<string, mixed>>
     */
    private function places(): array
    {
        return [
            [
                'name' => 'Stasiun MRT Bundaran HI',
                'slug' => 'stasiun-mrt-bundaran-hi',
                'category' => 'stasiun',
                'address' => 'Jl. M.H. Thamrin, Menteng, Jakarta Pusat, DKI Jakarta',
                'latitude' => -6.19375000,
                'longitude' => 106.82293000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => true,
                    'notes' => 'Akses penuh dari lobi hingga peron. Petugas siap membantu penumpang kursi roda.',
                ],
            ],
            [
                'name' => 'Stasiun MRT Blok M BCA',
                'slug' => 'stasiun-mrt-blok-m-bca',
                'category' => 'stasiun',
                'address' => 'Jl. Melawai, Kebayoran Baru, Jakarta Selatan, DKI Jakarta',
                'latitude' => -6.24437000,
                'longitude' => 106.80090000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => true,
                    'notes' => 'Terhubung langsung dengan Blok M Plaza melalui koridor aksesibel.',
                ],
            ],
            [
                'name' => 'Stasiun Gambir',
                'slug' => 'stasiun-gambir',
                'category' => 'stasiun',
                'address' => 'Jl. Medan Merdeka Timur, Gambir, Jakarta Pusat, DKI Jakarta',
                'latitude' => -6.17665000,
                'longitude' => 106.83070000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => false,
                    'notes' => 'Lift tersedia namun tombol belum berhuruf Braille di seluruh unit.',
                ],
            ],
            [
                'name' => 'Stasiun Tanah Abang',
                'slug' => 'stasiun-tanah-abang',
                'category' => 'stasiun',
                'address' => 'Jl. Jatibaru Raya, Tanah Abang, Jakarta Pusat, DKI Jakarta',
                'latitude' => -6.18562000,
                'longitude' => 106.81052000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => false,
                    'has_guiding_block' => true, 'wide_door' => false, 'braille_lift' => false,
                    'notes' => 'Padat saat jam sibuk, jalur pemandu sebagian tertutup pedagang.',
                ],
            ],
            [
                'name' => 'Grand Indonesia Shopping Town',
                'slug' => 'grand-indonesia-shopping-town',
                'category' => 'mal',
                'address' => 'Jl. M.H. Thamrin No.1, Menteng, Jakarta Pusat, DKI Jakarta',
                'latitude' => -6.19524000,
                'longitude' => 106.82080000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => true,
                    'notes' => 'Toilet disabilitas tersedia di setiap lantai. Kursi roda bisa dipinjam di lobby.',
                ],
            ],
            [
                'name' => 'Central Park Mall',
                'slug' => 'central-park-mall',
                'category' => 'mal',
                'address' => 'Jl. Letjen S. Parman Kav. 28, Grogol, Jakarta Barat, DKI Jakarta',
                'latitude' => -6.17718000,
                'longitude' => 106.79091000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => false,
                    'notes' => 'Akses baik, namun beberapa lift servis belum memiliki tombol Braille.',
                ],
            ],
            [
                'name' => 'Senayan City',
                'slug' => 'senayan-city',
                'category' => 'mal',
                'address' => 'Jl. Asia Afrika Lot 19, Gelora, Jakarta Pusat, DKI Jakarta',
                'latitude' => -6.22768000,
                'longitude' => 106.79777000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => false, 'wide_door' => true, 'braille_lift' => true,
                    'notes' => 'Ubin pemandu tunanetra belum merata di seluruh area lantai dasar.',
                ],
            ],
            [
                'name' => 'Mall Kelapa Gading',
                'slug' => 'mall-kelapa-gading',
                'category' => 'mal',
                'address' => 'Jl. Boulevard Kelapa Gading, Kelapa Gading, Jakarta Utara, DKI Jakarta',
                'latitude' => -6.15768000,
                'longitude' => 106.90560000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => false,
                    'notes' => 'Ramp tersedia di semua pintu masuk utama.',
                ],
            ],
            [
                'name' => 'AEON Mall BSD City',
                'slug' => 'aeon-mall-bsd-city',
                'category' => 'mal',
                'address' => 'Jl. BSD Raya Utama, Pagedangan, Tangerang, Banten',
                'latitude' => -6.30420000,
                'longitude' => 106.65340000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => true,
                    'notes' => 'Salah satu mal paling aksesibel di Tangerang. Ada kursi roda gratis di informasi.',
                ],
            ],
            [
                'name' => 'Summarecon Mall Serpong',
                'slug' => 'summarecon-mall-serpong',
                'category' => 'mal',
                'address' => 'Jl. Boulevard Gading Serpong, Kelapa Dua, Tangerang, Banten',
                'latitude' => -6.24041000,
                'longitude' => 106.62740000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => false,
                    'notes' => 'Akses ramah, area parkir disabilitas dekat pintu lobi utama.',
                ],
            ],
            [
                'name' => 'RSUPN Dr. Cipto Mangunkusumo',
                'slug' => 'rsupn-dr-cipto-mangunkusumo',
                'category' => 'rumah_sakit',
                'address' => 'Jl. Diponegoro No.71, Kenari, Jakarta Pusat, DKI Jakarta',
                'latitude' => -6.19755000,
                'longitude' => 106.84826000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => false,
                    'notes' => 'Ramp di semua gedung poli. Jalur pemandu tersedia dari lobi ke radiologi.',
                ],
            ],
            [
                'name' => 'RS Fatmawati',
                'slug' => 'rs-fatmawati',
                'category' => 'rumah_sakit',
                'address' => 'Jl. RS Fatmawati Raya No.39, Cilandak, Jakarta Selatan, DKI Jakarta',
                'latitude' => -6.26793000,
                'longitude' => 106.79364000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => false, 'wide_door' => true, 'braille_lift' => false,
                    'notes' => 'Lift pasien aksesibel. Ubin pemandu baru tersedia di gedung utama.',
                ],
            ],
            [
                'name' => 'RS Siloam Karawaci',
                'slug' => 'rs-siloam-karawaci',
                'category' => 'rumah_sakit',
                'address' => 'Jl. Boulevard Diponegoro No.105, Lippo Karawaci, Tangerang, Banten',
                'latitude' => -6.22924000,
                'longitude' => 106.59960000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => true,
                    'notes' => 'Fasilitas sangat aksesibel, tersedia layanan antar jemput kursi roda.',
                ],
            ],
            [
                'name' => 'RS Premier Bintaro',
                'slug' => 'rs-premier-bintaro',
                'category' => 'rumah_sakit',
                'address' => 'Jl. M.H. Thamrin No.1, Bintaro Jaya Sektor 7, Tangerang Selatan, Banten',
                'latitude' => -6.27615000,
                'longitude' => 106.70680000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => false,
                    'notes' => 'Toilet disabilitas luas dan bersih, mudah dijangkau dari IGD.',
                ],
            ],
            [
                'name' => 'Kopi Tuku Grand Indonesia',
                'slug' => 'kopi-tuku-grand-indonesia',
                'category' => 'kafe',
                'address' => 'Grand Indonesia West Mall, Jl. M.H. Thamrin No.1, Jakarta Pusat, DKI Jakarta',
                'latitude' => -6.19544000,
                'longitude' => 106.82100000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => false, 'wide_door' => true, 'braille_lift' => false,
                    'notes' => 'Area meja cukup luas untuk pengguna kursi roda.',
                ],
            ],
            [
                'name' => 'STARBUCKS Taman Anggrek',
                'slug' => 'starbucks-taman-anggrek',
                'category' => 'kafe',
                'address' => 'Mal Taman Anggrek Lt.1, Jl. Letjen S. Parman, Jakarta Barat, DKI Jakarta',
                'latitude' => -6.17842000,
                'longitude' => 106.79100000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => false,
                    'notes' => 'Meja aksesibel tersedia dekat jendela, area gerak cukup lega.',
                ],
            ],
            [
                'name' => 'Balai Kota Tangerang',
                'slug' => 'balai-kota-tangerang',
                'category' => 'fasilitas_publik',
                'address' => 'Jl. Satria Sudirman No.1, Sukarasa, Tangerang, Banten',
                'latitude' => -6.17830000,
                'longitude' => 106.63000000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => true, 'has_accessible_toilet' => true,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => false,
                    'notes' => 'Layanan publik dengan ramp di pintu utama dan lift ke lantai layanan.',
                ],
            ],
            [
                'name' => 'Taman Suropati',
                'slug' => 'taman-suropati',
                'category' => 'taman',
                'address' => 'Jl. Taman Suropati No.7, Menteng, Jakarta Pusat, DKI Jakarta',
                'latitude' => -6.19975000,
                'longitude' => 106.83364000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => true, 'has_lift' => false, 'has_accessible_toilet' => false,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => false,
                    'notes' => 'Jalur taman sebagian rata dan ramah kursi roda. Toilet belum aksesibel.',
                ],
            ],
            [
                'name' => 'Taman Langsat',
                'slug' => 'taman-langsat',
                'category' => 'taman',
                'address' => 'Jl. Kebayoran Baru, Gunung, Jakarta Selatan, DKI Jakarta',
                'latitude' => -6.23510000,
                'longitude' => 106.80230000,
                'is_verified' => true,
                'features' => [
                    'has_ramp' => false, 'has_lift' => false, 'has_accessible_toilet' => false,
                    'has_guiding_block' => true, 'wide_door' => true, 'braille_lift' => false,
                    'notes' => 'Jalur setapak masih tanah di beberapa titik, sulit untuk kursi roda.',
                ],
            ],
            [
                'name' => 'Hutan Kota By Plataran (GBK)',
                'slug' => 'hutan-kota-by-plataran-gbk',
                'category' => 'taman',
                'address' => 'Kawasan Gelora Bung Karno, Jl. Pintu Satu Senayan, Jakarta Pusat, DKI Jakarta',
                'latitude' => -6.21970000,
                'longitude' => 106.80230000,
                'is_verified' => false,
                'features' => [
                    'has_ramp' => true, 'has_lift' => false, 'has_accessible_toilet' => true,
                    'has_guiding_block' => false, 'wide_door' => true, 'braille_lift' => false,
                    'notes' => 'Area terbuka nyaman, jalur pedestrian lebar namun belum ada ubin pemandu.',
                ],
            ],
        ];
    }
}
