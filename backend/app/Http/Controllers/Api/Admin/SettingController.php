<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public const KEYS = [
        'site_name', 'site_tagline', 'address', 'phone', 'email',
        'facebook', 'youtube', 'map_embed', 'footer_text',
        'hero_title', 'hero_subtitle', 'established', 'registration_no',
    ];

    /** পাবলিক ও অ্যাডমিন দুই জায়গাতেই ব্যবহৃত: সেটিংস + logo_url */
    public static function payload(): array
    {
        $all = Setting::pluck('value', 'key')->all();
        $all['logo_url'] = ! empty($all['logo']) ? Storage::disk('public')->url($all['logo']) : null;

        return $all;
    }

    public function index(): JsonResponse
    {
        return response()->json(self::payload());
    }

    /** POST /admin/settings/logo */
    public function logo(Request $request): JsonResponse
    {
        $request->validate(['logo' => ['required', 'image', 'max:2048']]);

        if ($old = Setting::where('key', 'logo')->value('value')) {
            Storage::disk('public')->delete($old);
        }
        Setting::updateOrCreate(['key' => 'logo'], ['value' => $request->file('logo')->store('settings', 'public')]);

        return $this->index();
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:2000'],
        ])['settings'];

        foreach (array_intersect_key($data, array_flip(self::KEYS)) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return $this->index();
    }
}
