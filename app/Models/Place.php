<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Place extends Model
{
    use HasFactory;

    /**
     * Kategori tempat yang tersedia.
     *
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'stasiun' => 'Stasiun',
        'mal' => 'Mal / Pusat Perbelanjaan',
        'rumah_sakit' => 'Rumah Sakit',
        'kafe' => 'Kafe',
        'taman' => 'Taman',
        'fasilitas_publik' => 'Fasilitas Publik',
    ];

    /**
     * Label warna status kelayakan.
     *
     * @var array<string, string>
     */
    public const STATUS_LABELS = [
        'green' => 'Ramah',
        'yellow' => 'Terbatas',
        'red' => 'Berbahaya',
    ];

    protected $fillable = [
        'name',
        'slug',
        'category',
        'address',
        'latitude',
        'longitude',
        'overall_score',
        'status_color',
        'is_verified',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'overall_score' => 'integer',
            'is_verified' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $place) {
            if (empty($place->slug)) {
                $place->slug = static::generateUniqueSlug($place->name);
            }
        });

        static::updating(function (self $place) {
            if ($place->isDirty('name') && empty($place->slug)) {
                $place->slug = static::generateUniqueSlug($place->name);
            }
        });
    }

    /**
     * Buat slug unik dari nama tempat.
     */
    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function features(): HasOne
    {
        return $this->hasOne(AccessibilityFeature::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Label kategori yang ramah dibaca manusia.
     */
    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? Str::headline($this->category);
    }

    /**
     * Label warna status yang ramah dibaca manusia.
     */
    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status_color] ?? 'Tidak diketahui';
    }

    /**
     * Hex color untuk indikator Leaflet / UI.
     */
    public function statusHex(): string
    {
        return match ($this->status_color) {
            'green' => '#047857',
            'yellow' => '#a55a00',
            'red' => '#9f1239',
            default => '#6b7280',
        };
    }

    /**
     * Emoji indikator status.
     */
    public function statusEmoji(): string
    {
        return match ($this->status_color) {
            'green' => '🟢',
            'yellow' => '🟡',
            'red' => '🔴',
            default => '⚪',
        };
    }

    /**
     * Hitung ulang skor & warna dari fitur aksesibilitas terkait.
     */
    public function recalculateScore(): self
    {
        $features = $this->features;

        if ($features) {
            $this->overall_score = app(\App\Services\AccessibilityCalculator::class)->score($features);
            $this->status_color = app(\App\Services\AccessibilityCalculator::class)->color($this->overall_score);
            $this->saveQuietly();
        }

        return $this;
    }

    /**
     * Representasi ringkas untuk konsumsi JSON Leaflet.
     *
     * @return array<string, mixed>
     */
    public function toMapArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->category,
            'category_label' => $this->categoryLabel(),
            'address' => $this->address,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'overall_score' => $this->overall_score,
            'status_color' => $this->status_color,
            'status_label' => $this->statusLabel(),
            'status_hex' => $this->statusHex(),
            'is_verified' => $this->is_verified,
            'url' => route('place.show', $this->slug),
            'features' => $this->features ? [
                'has_ramp' => (bool) $this->features->has_ramp,
                'has_lift' => (bool) $this->features->has_lift,
                'has_accessible_toilet' => (bool) $this->features->has_accessible_toilet,
                'has_guiding_block' => (bool) $this->features->has_guiding_block,
                'wide_door' => (bool) $this->features->wide_door,
                'braille_lift' => (bool) $this->features->braille_lift,
                'notes' => $this->features->notes,
            ] : null,
        ];
    }
}
