<?php

namespace App\Models;

use App\Services\AccessibilityCalculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessibilityFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id',
        'has_ramp',
        'has_lift',
        'has_accessible_toilet',
        'has_guiding_block',
        'wide_door',
        'braille_lift',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'has_ramp' => 'boolean',
            'has_lift' => 'boolean',
            'has_accessible_toilet' => 'boolean',
            'has_guiding_block' => 'boolean',
            'wide_door' => 'boolean',
            'braille_lift' => 'boolean',
        ];
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * Daftar fitur boolean yang dinilai, dengan bobot & labelnya.
     *
     * @return array<int, array{key: string, label: string, weight: int, icon: string}>
     */
    public static function criteria(): array
    {
        return [
            ['key' => 'has_ramp', 'label' => 'Ramp / Bidang Miring', 'weight' => 20, 'icon' => 'ramp'],
            ['key' => 'has_lift', 'label' => 'Lift Ramah Disabilitas', 'weight' => 20, 'icon' => 'lift'],
            ['key' => 'has_accessible_toilet', 'label' => 'Toilet Disabilitas', 'weight' => 15, 'icon' => 'toilet'],
            ['key' => 'has_guiding_block', 'label' => 'Ubin Pemandu Tunanetra', 'weight' => 15, 'icon' => 'guiding-block'],
            ['key' => 'wide_door', 'label' => 'Pintu Lebar (>90 cm)', 'weight' => 15, 'icon' => 'door'],
            ['key' => 'braille_lift', 'label' => 'Tombol Lift Braille', 'weight' => 15, 'icon' => 'braille'],
        ];
    }

    /**
     * Skor aksesibilitas (0-100).
     */
    public function score(): int
    {
        return app(AccessibilityCalculator::class)->score($this);
    }

    /**
     * Warna status berdasarkan skor.
     */
    public function color(): string
    {
        return app(AccessibilityCalculator::class)->color($this->score());
    }

    /**
     * @return array<int, array{key: string, label: string, icon: string, available: bool}>
     */
    public function checklist(): array
    {
        return array_map(fn (array $criterion) => [
            'key' => $criterion['key'],
            'label' => $criterion['label'],
            'icon' => $criterion['icon'],
            'available' => (bool) $this->{$criterion['key']},
        ], self::criteria());
    }
}
