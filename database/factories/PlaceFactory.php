<?php

namespace Database\Factories;

use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Place>
 */
class PlaceFactory extends Factory
{
    protected $model = Place::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 100000),
            'category' => fake()->randomElement(array_keys(Place::CATEGORIES)),
            'address' => fake()->address(),
            'latitude' => fake()->latitude(-7, -6),
            'longitude' => fake()->longitude(106, 107),
            'overall_score' => 0,
            'status_color' => 'yellow',
            'is_verified' => true,
            'created_by' => User::factory(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['is_verified' => false]);
    }
}
