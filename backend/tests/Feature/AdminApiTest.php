<?php

namespace Tests\Feature;

use App\Models\MembershipApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertUnprocessable();
    }

    public function test_admin_routes_require_authentication(): void
    {
        $this->getJson('/api/v1/admin/notices')->assertUnauthorized();
    }

    public function test_editor_can_manage_notices_but_not_members(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'editor']));

        $this->postJson('/api/v1/admin/notices', ['title' => 'New notice'])->assertCreated();
        $this->getJson('/api/v1/admin/members')->assertForbidden();
    }

    public function test_admin_can_create_member_and_only_super_admin_sees_users(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));

        $this->postJson('/api/v1/admin/members', ['member_no' => '0100', 'name' => 'Karim'])->assertCreated();
        $this->getJson('/api/v1/admin/users')->assertForbidden();
    }

    public function test_only_one_committee_is_current(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'super_admin']));

        $this->postJson('/api/v1/admin/committees', ['title' => 'EC', 'term_start' => 2024, 'term_end' => 2025, 'is_current' => true])->assertCreated();
        $this->postJson('/api/v1/admin/committees', ['title' => 'EC', 'term_start' => 2026, 'term_end' => 2027, 'is_current' => true])->assertCreated();

        $this->assertDatabaseCount('committees', 2);
        $this->assertSame(1, \App\Models\Committee::where('is_current', true)->count());
    }

    public function test_approving_application_creates_member(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $application = MembershipApplication::create(['name' => 'Applicant', 'phone' => '017']);

        $this->postJson("/api/v1/admin/membership-applications/{$application->id}/approve")->assertOk();

        $this->assertDatabaseHas('members', ['name' => 'Applicant', 'member_no' => '0001']);
        $this->assertSame('approved', $application->fresh()->status);
    }

    public function test_payments_are_admin_only_and_summary_works(): void
    {
        $member = \App\Models\Member::create(['member_no' => '0001', 'name' => 'Payer']);

        Sanctum::actingAs(User::factory()->create(['role' => 'editor']));
        $this->getJson('/api/v1/admin/payments')->assertForbidden();

        Sanctum::actingAs(User::factory()->create(['role' => 'admin']));
        $this->postJson('/api/v1/admin/payments', [
            'member_id' => $member->id, 'amount' => 500, 'paid_at' => now()->toDateString(),
        ])->assertCreated();

        $this->getJson('/api/v1/admin/payments/summary')->assertOk()->assertJsonPath('total', 500);
    }

    public function test_dashboard_hides_member_stats_from_editors(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'editor']));

        $this->getJson('/api/v1/admin/dashboard')->assertOk()->assertJsonMissingPath('members');
    }
}
