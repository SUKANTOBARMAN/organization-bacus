<?php

namespace Tests\Feature;

use App\Models\Designation;
use App\Models\Member;
use App\Models\Post;
use App\Models\Notice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_published_notices_are_listed(): void
    {
        Notice::create(['title' => 'Visible', 'is_published' => true, 'published_at' => now()]);
        Notice::create(['title' => 'Hidden', 'is_published' => false, 'published_at' => now()]);

        $this->getJson('/api/v1/notices')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Visible');
    }

    public function test_member_directory_hides_private_fields_and_private_members(): void
    {
        Member::create(['member_no' => '0001', 'name' => 'Public One', 'phone' => '017', 'email' => 'a@b.c', 'is_public' => true]);
        Member::create(['member_no' => '0002', 'name' => 'Private One', 'is_public' => false]);

        $response = $this->getJson('/api/v1/members')->assertOk()->assertJsonCount(1, 'data');

        $response->assertJsonMissingPath('data.0.phone')->assertJsonMissingPath('data.0.email');
    }

    public function test_contact_form_stores_message(): void
    {
        $this->postJson('/api/v1/contact', [
            'name' => 'Rahim', 'email' => 'rahim@example.com', 'message' => 'Hello',
        ])->assertCreated();

        $this->assertDatabaseHas('contact_messages', ['email' => 'rahim@example.com']);
    }

    public function test_contact_form_rejects_honeypot(): void
    {
        $this->postJson('/api/v1/contact', [
            'name' => 'Bot', 'email' => 'bot@example.com', 'message' => 'spam', 'website' => 'http://spam',
        ])->assertUnprocessable();
    }

    public function test_members_are_sorted_by_designation_rank(): void
    {
        $president = Designation::create(['name' => 'President', 'rank' => 10]);
        $general = Designation::create(['name' => 'General Member', 'rank' => 300]);

        Member::create(['member_no' => '0001', 'name' => 'Aaa General', 'designation_id' => $general->id]);
        Member::create(['member_no' => '0002', 'name' => 'Zzz President', 'designation_id' => $president->id]);

        $this->getJson('/api/v1/members')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Zzz President')
            ->assertJsonPath('data.0.designation', 'President')
            ->assertJsonPath('data.1.name', 'Aaa General');
    }

    public function test_post_detail_returns_images_and_counts_views(): void
    {
        $post = Post::create(['title' => 'Event', 'type' => 'news', 'published_at' => now()]);
        $post->images()->create(['image' => 'post_images/a.jpg']);

        $this->getJson("/api/v1/posts/{$post->id}")
            ->assertOk()
            ->assertJsonCount(1, 'images');

        $this->assertSame(1, $post->fresh()->views_count);
    }

    public function test_membership_application_can_be_submitted(): void
    {
        $this->postJson('/api/v1/membership-applications', ['name' => 'Applicant', 'phone' => '017'])
            ->assertCreated();

        $this->assertDatabaseHas('membership_applications', ['name' => 'Applicant', 'status' => 'pending']);
    }
}
