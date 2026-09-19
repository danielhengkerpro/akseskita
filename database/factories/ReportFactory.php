<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'place_id' => null,
            'place_name' => fake()->company(),
            'latitude' => fake()->latitude(-7, -6),
            'longitude' => fake()->longitude(106, 107),
            'description' => fake()->paragraph(),
            'photo_path' => null,
            'status' => Report::STATUS_PENDING,
            'reviewed_by' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => Report::STATUS_APPROVED]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => Report::STATUS_REJECTED]);
    }
}
