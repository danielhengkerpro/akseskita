<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_pending_report(): void
    {
        $admin = User::factory()->admin()->create();
        $report = Report::factory()->create(['status' => Report::STATUS_PENDING]);

        $this->actingAs($admin)
            ->post(route('admin.moderation.approve', $report))
            ->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => Report::STATUS_APPROVED,
            'reviewed_by' => $admin->id,
        ]);
    }

    public function test_admin_can_reject_pending_report(): void
    {
        $admin = User::factory()->admin()->create();
        $report = Report::factory()->create(['status' => Report::STATUS_PENDING]);

        $this->actingAs($admin)
            ->post(route('admin.moderation.reject', $report))
            ->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => Report::STATUS_REJECTED,
            'reviewed_by' => $admin->id,
        ]);
    }

    public function test_report_already_approved_cannot_be_moderated_again(): void
    {
        $admin = User::factory()->admin()->create();
        $report = Report::factory()->approved()->create();

        $this->actingAs($admin)
            ->post(route('admin.moderation.reject', $report))
            ->assertForbidden();

        // Status tetap approved (tidak berubah).
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => Report::STATUS_APPROVED,
        ]);
    }
}
