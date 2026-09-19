<?php

namespace App\Services;

use App\Models\AccessibilityFeature;

class AccessibilityCalculator
{
    /**
     * Ambang batas skor untuk setiap warna status.
     */
    public const GREEN_THRESHOLD = 70;

    public const YELLOW_THRESHOLD = 40;

    /**
     * Hitung skor aksesibilitas (0-100) dari fitur yang tersedia.
     * Menggunakan bobot terbobot dari AccessibilityFeature::criteria().
     */
    public function score(AccessibilityFeature $features): int
    {
        $earned = 0;
        $total = 0;

        foreach (AccessibilityFeature::criteria() as $criterion) {
            $weight = $criterion['weight'];
            $total += $weight;

            if ($features->{$criterion['key']}) {
                $earned += $weight;
            }
        }

        if ($total === 0) {
            return 0;
        }

        return (int) round(($earned / $total) * 100);
    }

    /**
     * Tentukan warna status berdasarkan skor.
     */
    public function color(int $score): string
    {
        return match (true) {
            $score >= self::GREEN_THRESHOLD => 'green',
            $score >= self::YELLOW_THRESHOLD => 'yellow',
            default => 'red',
        };
    }

    /**
     * Emoji indikator dari warna.
     */
    public function emoji(string $color): string
    {
        return match ($color) {
            'green' => '🟢',
            'yellow' => '🟡',
            'red' => '🔴',
            default => '⚪',
        };
    }

    /**
     * Label deskriptif dari warna.
     */
    public function label(string $color): string
    {
        return match ($color) {
            'green' => 'Ramah',
            'yellow' => 'Terbatas',
            'red' => 'Berbahaya',
            default => 'Tidak diketahui',
        };
    }
}
