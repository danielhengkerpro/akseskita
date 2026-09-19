<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_USER = 'user';

    public const ROLE_ADMIN = 'admin';

    public const ACCOUNT_DISABILITY = 'disability';

    public const ACCOUNT_CONTRIBUTOR = 'contributor';

    public const DISABILITY_TYPES = [
        'wheelchair' => [
            'label' => 'Disabilitas Fisik / Kursi Roda',
            'icon' => 'wheelchair',
            'badge' => '♿ Kursi Roda / Fisik',
            'description' => 'Membutuhkan ramp landai, lift ramah kursi roda, toilet disabilitas, dan jalur tanpa tangga.',
            'priorities' => ['has_ramp', 'has_lift', 'has_accessible_toilet', 'wide_door'],
        ],
        'blind' => [
            'label' => 'Disabilitas Netra / Low Vision',
            'icon' => 'vision',
            'badge' => '🦯 Netra / Low Vision',
            'description' => 'Membutuhkan ubin pemandu (guiding block), tombol Braille, dan teks dengan kontras tinggi.',
            'priorities' => ['has_guiding_block', 'braille_lift'],
        ],
        'deaf' => [
            'label' => 'Disabilitas Rungu / Tuli',
            'icon' => 'hearing',
            'badge' => '🧏 Rungu / Tuli',
            'description' => 'Membutuhkan papan informasi visual yang jelas dan petunjuk tekstual.',
            'priorities' => [],
        ],
        'elderly' => [
            'label' => 'Lansia & Mobilitas Terbatas',
            'icon' => 'elderly',
            'badge' => '🚶 Lansia / Langkah Terbatas',
            'description' => 'Membutuhkan jalur landai dengan pegangan tangan (handrail), lift, dan minim undakan.',
            'priorities' => ['has_ramp', 'has_lift'],
        ],
        'other' => [
            'label' => 'Disabilitas Lainnya / Sensorik',
            'icon' => 'community',
            'badge' => '🧩 Kebutuhan Khusus',
            'description' => 'Kebutuhan aksesibilitas terpadu dan fleksibel.',
            'priorities' => ['has_ramp', 'has_lift', 'has_accessible_toilet'],
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'account_type',
        'disability_type',
        'accessibility_preferences',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'accessibility_preferences' => 'array',
        ];
    }

    /**
     * Laporan yang dibuat oleh user ini.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Tempat yang dibuat/dikelola oleh user ini.
     */
    public function places(): HasMany
    {
        return $this->hasMany(Place::class, 'created_by');
    }

    /**
     * Laporan yang sudah diverifikasi oleh user ini (admin).
     */
    public function reviewedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'reviewed_by');
    }

    /**
     * Cek apakah user adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Cek apakah user adalah user biasa (masyarakat/relawan).
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * URL avatar user, dengan fallback inisial nama.
     */
    public function avatarUrl(): ?string
    {
        return $this->avatar ? asset('storage/'.$this->avatar) : null;
    }

    /**
     * Cek apakah akun bertipe penyandang disabilitas.
     */
    public function isDisabilityAccount(): bool
    {
        return $this->account_type === self::ACCOUNT_DISABILITY;
    }

    /**
     * Dapatkan detail ragam disabilitas pengguna.
     */
    public function getDisabilityInfo(): ?array
    {
        if (! $this->isDisabilityAccount() || ! $this->disability_type) {
            return null;
        }

        return self::DISABILITY_TYPES[$this->disability_type] ?? [
            'label' => 'Penyandang Disabilitas',
            'icon' => 'wheelchair',
            'badge' => '♿ Disabilitas',
            'description' => 'Aksesibilitas ramah disabilitas',
            'priorities' => ['has_ramp', 'has_lift'],
        ];
    }

    /**
     * Label tipe akun pengguna.
     */
    public function getAccountTypeLabel(): string
    {
        if ($this->isAdmin()) {
            return 'Admin Verifikator';
        }

        if ($this->isDisabilityAccount()) {
            return $this->getDisabilityInfo()['badge'] ?? 'Akun Disabilitas';
        }

        return 'Relawan / Kontributor';
    }
}
