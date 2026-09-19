<?php

namespace Tests\Feature;

use App\Models\Place;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_dashboard_summary_counts_are_correct(): void
    {
        $user = User::factory()->user()->create();

        Report::factory()->create(['user_id' => $user->id, 'status' => Report::STATUS_PENDING]);
        Report::factory()->create(['user_id' => $user->id, 'status' => Report::STATUS_PENDING]);
        Report::factory()->create(['user_id' => $user->id, 'status' => Report::STATUS_APPROVED]);
        Report::factory()->create(['user_id' => $user->id, 'status' => Report::STATUS_REJECTED]);

        // Laporan milik user lain tidak boleh terhitung.
        Report::factory()->create(['status' => Report::STATUS_PENDING]);

        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertOk();
        $response->assertViewHas('summary', function (array $summary) {
            return $summary === [
                'total' => 4,
                'pending' => 2,
                'approved' => 1,
                'rejected' => 1,
            ];
        });
    }

    public function test_admin_dashboard_stats_are_correct(): void
    {
        $admin = User::factory()->admin()->create();

        Place::factory()->create(['status_color' => 'green', 'is_verified' => true]);
        Place::factory()->create(['status_color' => 'yellow', 'is_verified' => false]);
        Place::factory()->create(['status_color' => 'green', 'is_verified' => true]);

        Report::factory()->create(['status' => Report::STATUS_PENDING]);
        Report::factory()->create(['status' => Report::STATUS_APPROVED]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('stats', function (array $stats) {
            return $stats['places'] === 3
                && $stats['verified'] === 2
                && $stats['green'] === 2
                && $stats['yellow'] === 1
                && $stats['red'] === 0
                && $stats['reports_total'] === 2
                && $stats['reports_pending'] === 1
                && $stats['reports_approved'] === 1
                && $stats['reports_rejected'] === 0;
        });
    }

    public function test_moderation_counts_are_correct(): void
    {
        $admin = User::factory()->admin()->create();

        Report::factory()->count(2)->create(['status' => Report::STATUS_PENDING]);
        Report::factory()->create(['status' => Report::STATUS_APPROVED]);
        Report::factory()->create(['status' => Report::STATUS_REJECTED]);

        $response = $this->actingAs($admin)->get(route('admin.moderation.index'));

        $response->assertOk();
        $response->assertViewHas('counts', function (array $counts) {
            return $counts === [
                'all' => 4,
                'pending' => 2,
                'approved' => 1,
                'rejected' => 1,
            ];
        });
    }
}
