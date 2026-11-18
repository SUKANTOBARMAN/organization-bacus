<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\CommissionMember;
use App\Models\Committee;
use App\Models\ContactMessage;
use App\Models\Document;
use App\Models\Election;
use App\Models\Member;
use App\Models\MembershipApplication;
use App\Models\Notice;
use App\Models\Page;
use App\Models\Post;
use App\Models\Slider;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** ওয়েবসাইটের পাবলিক (লগইন ছাড়া) এন্ডপয়েন্ট — শুধু পাবলিশ করা তথ্য। */
class PublicController extends Controller
{
    public function settings(): JsonResponse
    {
        return response()->json(SettingController::payload());
    }

    public function home(): JsonResponse
    {
        return response()->json([
            'sliders' => Slider::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get(),
            'notices' => $this->noticeQuery()->limit(6)->get(),
            'featured' => $this->postQuery(null)->where('is_featured', true)->limit(3)->get(),
            'news' => $this->postQuery('news')->limit(4)->get(),
            'events' => Post::where('is_published', true)->where('type', 'event')
                ->where('event_at', '>=', now())->orderBy('event_at')->limit(4)->get(),
            'committee' => Committee::with('members')->where('type', 'executive')->where('is_current', true)->first(),
            'albums' => Album::where('is_published', true)->latest('id')->limit(4)->get(),
            'stats' => [
                'members' => Member::publicListed()->count(),
                'notices' => Notice::where('is_published', true)->count(),
                'events' => Post::where('is_published', true)->where('type', 'event')->count(),
                'albums' => Album::where('is_published', true)->count(),
            ],
        ]);
    }

    public function sliders(): JsonResponse
    {
        return response()->json(Slider::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get());
    }

    public function page(string $slug): JsonResponse
    {
        return response()->json(Page::where('slug', $slug)->where('is_published', true)->firstOrFail());
    }

    /** বর্তমান কার্যনির্বাহী কমিটি (?type=advisory দিলে উপদেষ্টা পরিষদ) */
    public function committee(Request $request): JsonResponse
    {
        $type = in_array($request->query('type'), ['executive', 'advisory', 'sub_committee'], true) ? $request->query('type') : 'executive';

        return response()->json(Committee::with('members')->where('type', $type)->where('is_current', true)->first());
    }

    public function committees(Request $request): JsonResponse
    {
        $query = Committee::orderByDesc('term_start');
        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        return response()->json($query->get());
    }

    public function committeeShow($id): JsonResponse
    {
        return response()->json(Committee::with('members')->findOrFail($id));
    }

    public function commission(): JsonResponse
    {
        return response()->json([
            'members' => CommissionMember::where('is_current', true)->orderBy('sort_order')->get(),
            'elections' => Election::where('is_published', true)->orderByDesc('election_date')->get(),
        ]);
    }

    public function electionShow($id): JsonResponse
    {
        return response()->json(Election::with('candidates')->where('is_published', true)->findOrFail($id));
    }

    public function notices(Request $request)
    {
        $query = $this->noticeQuery();

        if (in_array($request->query('category'), ['notice', 'circular', 'tender', 'result', 'press_release'], true)) {
            $query->where('category', $request->query('category'));
        }

        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->paginate(15);
    }

    public function noticeShow($id): JsonResponse
    {
        return response()->json(Notice::where('is_published', true)->findOrFail($id));
    }

    public function posts(Request $request)
    {
        $type = $request->query('type');
        $query = $this->postQuery(in_array($type, ['news', 'event'], true) ? $type : null)->withCount('images');

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }
        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->paginate(12);
    }

    /** বিস্তারিত: সব ছবিসহ, আর ভিউ কাউন্ট বাড়ে */
    public function postShow($id): JsonResponse
    {
        $post = Post::with('images')->where('is_published', true)->findOrFail($id);
        $post->increment('views_count');

        return response()->json($post);
    }

    public function albums()
    {
        return Album::withCount('photos')->where('is_published', true)->orderByDesc('event_date')->orderByDesc('id')->paginate(12);
    }

    public function albumShow($id): JsonResponse
    {
        return response()->json(Album::with('photos')->where('is_published', true)->findOrFail($id));
    }

    public function videos(): JsonResponse
    {
        return response()->json(Video::where('is_published', true)->orderBy('sort_order')->orderByDesc('id')->get());
    }

    public function documents(Request $request): JsonResponse
    {
        $query = Document::where('is_published', true)->orderBy('sort_order')->orderBy('id');

        if (in_array($request->query('type'), ['constitution', 'rules', 'minutes', 'annual_report', 'form', 'download'], true)) {
            $query->where('type', $request->query('type'));
        }

        return response()->json($query->get());
    }

    /**
     * পাবলিক ডিরেক্টরি: পদবির র‍্যাঙ্ক অনুযায়ী সাজানো।
     * শুধু নম্বর, নাম, পদবি, ধরন, ছবি। ফোন/ইমেইল/ঠিকানা কখনো যায় না।
     */
    public function members(Request $request)
    {
        $query = Member::query()
            ->leftJoin('designations', 'designations.id', '=', 'members.designation_id')
            ->where('members.status', 'active')
            ->where('members.is_public', true)
            ->select([
                'members.id', 'members.member_no', 'members.name', 'members.photo', 'members.membership_type',
                DB::raw('COALESCE(designations.name, members.designation) as designation'),
            ])
            ->orderByRaw('COALESCE(designations.rank, 9999)')
            ->orderBy('members.name');

        if ($search = $request->query('search')) {
            $query->where(fn ($w) => $w->where('members.name', 'like', "%{$search}%")->orWhere('members.member_no', 'like', "%{$search}%"));
        }
        if (in_array($request->query('type'), ['general', 'life', 'associate', 'honorary'], true)) {
            $query->where('members.membership_type', $request->query('type'));
        }

        return $query->paginate(30);
    }

    /** সদস্য হওয়ার আবেদন */
    public function membershipApply(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'profession' => ['nullable', 'string', 'max:255'],
            'workplace' => ['nullable', 'string', 'max:255'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'membership_type' => ['sometimes', 'in:general,life,associate'],
            'message' => ['nullable', 'string', 'max:3000'],
            'photo' => ['nullable', 'image', 'max:3072'],
            'website' => ['nullable', 'max:0'], // honeypot
        ]);

        unset($data['website'], $data['photo']);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('membership_applications', 'public');
        }

        MembershipApplication::create($data + ['ip' => $request->ip()]);

        return response()->json(['message' => 'আপনার আবেদন জমা হয়েছে। যাচাইয়ের পর আমরা যোগাযোগ করব।'], 201);
    }

    public function contact(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'max:0'], // honeypot: বট পূরণ করলে বাতিল
        ]);

        unset($data['website']);
        ContactMessage::create($data + ['ip' => $request->ip()]);

        return response()->json(['message' => 'আপনার বার্তা পাঠানো হয়েছে। ধন্যবাদ।'], 201);
    }

    private function noticeQuery()
    {
        return Notice::where('is_published', true)
            ->orderByDesc('is_pinned')->orderByDesc('published_at')->orderByDesc('id');
    }

    private function postQuery(?string $type)
    {
        $query = Post::where('is_published', true)->orderByDesc('published_at')->orderByDesc('id');

        return $type ? $query->where('type', $type) : $query;
    }
}
