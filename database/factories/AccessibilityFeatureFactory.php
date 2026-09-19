<?php

namespace Database\Factories;

use App\Models\AccessibilityFeature;
use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccessibilityFeature>
 */
class AccessibilityFeatureFactory extends Factory
{
    protected $model = AccessibilityFeature::class;

    public function definition(): array
    {
        return [
            'place_id' => Place::factory(),
            'has_ramp' => fake()->boolean(),
            'has_lift' => fake()->boolean(),
            'has_accessible_toilet' => fake()->boolean(),
            'has_guiding_block' => fake()->boolean(),
            'wide_door' => fake()->boolean(),
            'braille_lift' => fake()->boolean(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Semua fitur tersedia (skor 100).
     */
    public function all(): static
    {
        return $this->state(fn () => [
            'has_ramp' => true,
            'has_lift' => true,
            'has_accessible_toilet' => true,
            'has_guiding_block' => true,
            'wide_door' => true,
            'braille_lift' => true,
        ]);
    }

    /**
     * Tidak ada fitur tersedia (skor 0).
     */
    public function none(): static
    {
        return $this->state(fn () => [
            'has_ramp' => false,
            'has_lift' => false,
            'has_accessible_toilet' => false,
            'has_guiding_block' => false,
            'wide_door' => false,
            'braille_lift' => false,
        ]);
    }
}
