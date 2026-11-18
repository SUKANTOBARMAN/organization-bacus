<?php

namespace Database\Seeders;

use App\Models\CommissionMember;
use App\Models\Committee;
use App\Models\Designation;
use App\Models\Election;
use App\Models\ElectionCandidate;
use App\Models\Member;
use App\Models\Notice;
use App\Models\Post;
use App\Models\Video;
use Illuminate\Database\Seeder;

/** শুধু লোকাল এনভায়রনমেন্টে চলে — ডেমো ডাটা। */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $rank = fn (string $name) => Designation::where('name', $name)->first();

        $roles = ['President', 'Vice President', 'General Secretary', 'Treasurer', 'Life Member', 'General Member'];
        for ($i = 1; $i <= 14; $i++) {
            $designation = $rank($roles[$i % count($roles)]);
            Member::firstOrCreate(['member_no' => sprintf('%04d', $i)], [
                'name' => "Member {$i}",
                'designation_id' => $designation?->id,
                'membership_type' => $i % 5 === 0 ? 'life' : 'general',
                'blood_group' => ['A+', 'B+', 'O+', 'AB+'][$i % 4],
                'profession' => 'Service holder',
                'phone' => '01700-0000'.sprintf('%02d', $i),
                'email' => "member{$i}@example.org",
                'joined_at' => now()->subMonths($i)->toDateString(),
                'is_public' => $i !== 14,
            ]);
        }

        $committee = Committee::firstOrCreate(
            ['title' => 'Executive Committee', 'term_start' => 2026, 'term_end' => 2027],
            ['is_current' => true, 'type' => 'executive'],
        );

        if ($committee->members()->count() === 0) {
            foreach (['President', 'Vice President', 'General Secretary', 'Treasurer', 'Executive Member'] as $name) {
                $designation = $rank($name);
                $committee->members()->create([
                    'name' => 'Name Surname',
                    'designation' => $name,
                    'designation_id' => $designation?->id,
                    'sort_order' => $designation?->rank ?? 100,
                ]);
            }
        }

        CommissionMember::firstOrCreate(['designation' => 'Chief Election Commissioner'], ['name' => 'Name Surname']);

        $election = Election::firstOrCreate(['title' => 'Executive Committee Election 2026'], [
            'election_date' => now()->addMonths(2)->toDateString(),
            'status' => 'upcoming',
        ]);
        if ($election->candidates()->count() === 0) {
            ElectionCandidate::create(['election_id' => $election->id, 'name' => 'Candidate One', 'position' => 'President']);
            ElectionCandidate::create(['election_id' => $election->id, 'name' => 'Candidate Two', 'position' => 'President']);
        }

        foreach (['Notice', 'Circular'] as $i => $category) {
            Notice::firstOrCreate(['title' => "Sample {$category} title"], [
                'category' => strtolower($category),
                'body' => 'Sample body.',
                'published_at' => now()->subDays($i)->toDateString(),
            ]);
        }

        Post::firstOrCreate(['title' => 'Sample news headline'], [
            'type' => 'news', 'is_featured' => true, 'excerpt' => 'A short summary of the news.', 'published_at' => now()->toDateString(),
        ]);
        Post::firstOrCreate(['title' => 'Upcoming annual gathering'], [
            'type' => 'event', 'event_at' => now()->addDays(14), 'venue' => 'Community hall', 'published_at' => now()->toDateString(),
        ]);

        Video::firstOrCreate(['title' => 'Sample video'], ['url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']);
    }
}
