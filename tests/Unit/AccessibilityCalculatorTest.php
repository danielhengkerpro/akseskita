<?php

namespace Tests\Unit;

use App\Models\AccessibilityFeature;
use App\Services\AccessibilityCalculator;
use PHPUnit\Framework\TestCase;

class AccessibilityCalculatorTest extends TestCase
{
    private AccessibilityCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new AccessibilityCalculator;
    }

    public function test_all_features_score_100_and_green(): void
    {
        $features = new AccessibilityFeature([
            'has_ramp' => true,
            'has_lift' => true,
            'has_accessible_toilet' => true,
            'has_guiding_block' => true,
            'wide_door' => true,
            'braille_lift' => true,
        ]);

        $this->assertSame(100, $this->calculator->score($features));
        $this->assertSame('green', $this->calculator->color(100));
    }

    public function test_no_features_score_0_and_red(): void
    {
        $features = new AccessibilityFeature([
            'has_ramp' => false,
            'has_lift' => false,
            'has_accessible_toilet' => false,
            'has_guiding_block' => false,
            'wide_door' => false,
            'braille_lift' => false,
        ]);

        $this->assertSame(0, $this->calculator->score($features));
        $this->assertSame('red', $this->calculator->color(0));
    }

    public function test_ramp_and_lift_only_sum_to_40(): void
    {
        // Bobot ramp 20 + lift 20 = 40 dari total 100.
        $features = new AccessibilityFeature([
            'has_ramp' => true,
            'has_lift' => true,
            'has_accessible_toilet' => false,
            'has_guiding_block' => false,
            'wide_door' => false,
            'braille_lift' => false,
        ]);

        $this->assertSame(40, $this->calculator->score($features));
        $this->assertSame('yellow', $this->calculator->color(40));
    }

    public function test_color_thresholds_are_respected(): void
    {
        $this->assertSame('green', $this->calculator->color(70));
        $this->assertSame('yellow', $this->calculator->color(69));
        $this->assertSame('yellow', $this->calculator->color(40));
        $this->assertSame('red', $this->calculator->color(39));
    }
}
