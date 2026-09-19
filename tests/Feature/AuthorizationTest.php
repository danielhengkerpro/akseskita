<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->user()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_cannot_access_user_only_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('user.report.create'))
            ->assertForbidden();
    }

    public function test_user_can_access_report_create_form(): void
    {
        $user = User::factory()->user()->create();

        $this->actingAs($user)
            ->get(route('user.report.create'))
            ->assertOk();
    }

    public function test_regular_user_cannot_moderate_reports(): void
    {
        $user = User::factory()->user()->create();
        $report = Report::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.moderation.approve', $report))
            ->assertForbidden();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => Report::STATUS_PENDING,
        ]);
    }
}
