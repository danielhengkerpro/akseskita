<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /**
     * Label status yang ramah dibaca manusia.
     *
     * @var array<string, string>
     */
    public const STATUS_LABELS = [
        'pending' => 'Menunggu Verifikasi',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
    ];

    protected $fillable = [
        'user_id',
        'place_id',
        'place_name',
        'latitude',
        'longitude',
        'description',
        'photo_path',
        'status',
        'reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Label status yang ramah dibaca manusia.
     */
    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    /**
     * Warna badge Tailwind berdasarkan status.
     */
    public function statusColorClasses(): string
    {
        return match ($this->status) {
            'approved' => 'bg-go-50 text-go-700 ring-go-600/25',
            'rejected' => 'bg-stop-50 text-stop-700 ring-stop-600/25',
            default => 'bg-warn-50 text-warn-700 ring-warn-600/25',
        };
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * URL publik foto bukti laporan.
     */
    public function photoUrl(): ?string
    {
        return $this->photo_path ? asset('storage/'.$this->photo_path) : null;
    }
}
