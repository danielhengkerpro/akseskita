<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Guard regresi kontras WCAG. Menguji palet yang dipakai nyata di view
 * agar penurunan kontras di masa depan langsung terdeteksi.
 */
class ContrastRatioTest extends TestCase
{
    /** Ambang WCAG */
    private const AA_NORMAL = 4.5;

    private const AA_LARGE = 3.0;

    private static function srgb(float $c): float
    {
        $c /= 255;

        return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
    }

    private static function luminance(string $hex): float
    {
        $hex = ltrim($hex, '#');

        return 0.2126 * self::srgb(hexdec(substr($hex, 0, 2)))
            + 0.7152 * self::srgb(hexdec(substr($hex, 2, 2)))
            + 0.0722 * self::srgb(hexdec(substr($hex, 4, 2)));
    }

    private static function ratio(string $fg, string $bg): float
    {
        $a = self::luminance($fg);
        $b = self::luminance($bg);
        $hi = max($a, $b);
        $lo = min($a, $b);

        return ($hi + 0.05) / ($lo + 0.05);
    }

    /**
     * @dataProvider pairs
     */
    public function test_pairs_meet_minimum_contrast(string $label, string $fg, string $bg, float $min): void
    {
        $ratio = self::ratio($fg, $bg);

        $this->assertGreaterThanOrEqual(
            $min,
            round($ratio, 2),
            sprintf('%s: rasio %.2f < %.1f', $label, $ratio, $min)
        );
    }

    public static function pairs(): array
    {
        $W = 'ffffff';
        $P = 'f6f8fc';
        $S50 = 'f8fafc';
        $BD = '0a1628';

        return [
            // Palet teks (override keterbacaan)
            ['slate-400 di putih', '5b6a80', $W, self::AA_NORMAL],
            ['slate-400 di paper', '5b6a80', $P, self::AA_NORMAL],
            ['slate-400 di slate-50', '5b6a80', $S50, self::AA_NORMAL],
            ['slate-500 di putih', '556575', $W, self::AA_NORMAL],
            ['slate-500 di paper', '556575', $P, self::AA_NORMAL],
            ['slate-300 di putih', '64748b', $W, self::AA_NORMAL],
            ['slate-200 nomor di putih', '7c8ca0', $W, self::AA_LARGE],
            ['slate-600 di paper', '475569', $P, self::AA_NORMAL],
            ['ink-400 di paper', '5b6a80', $P, self::AA_NORMAL],
            // Aksi
            ['putih di go-600', $W, '047857', self::AA_NORMAL],
            ['putih di go-700', $W, '036b4d', self::AA_NORMAL],
            ['putih di route-cyan', $W, '0e7490', self::AA_NORMAL],
            ['route-cyan sebagai teks di putih', '0e7490', $W, self::AA_NORMAL],
            ['route-cyan-ink di putih', '0c4a5e', $W, self::AA_NORMAL],
            ['putih di stop-600', $W, 'be123c', self::AA_NORMAL],
            ['warn-700 di warn-50', 'a55a00', 'fff6e5', self::AA_NORMAL],
            ['emerald-700 ikon di go-50', '047857', 'e6f9f1', self::AA_LARGE],
            // Latar gelap
            ['white/80 di go-700', 'cfe0dc', '036b4d', self::AA_NORMAL],
            ['white/50 di board-900', '8f9ba7', $BD, self::AA_NORMAL],
            ['emerald-200 di go-700', 'a7f3d0', '036b4d', self::AA_NORMAL],
            // Mode kontras tinggi (AAA)
            ['HC kuning di hitam', 'ffff00', '000000', 7.0],
            ['HC putih di hitam', 'ffffff', '000000', 7.0],
            ['HC hitam di kuning', '000000', 'ffff00', 7.0],
        ];
    }
}
