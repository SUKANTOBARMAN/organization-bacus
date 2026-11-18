<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_name' => 'Organization Name',
            'site_tagline' => 'Together, we build a stronger community',
            'address' => 'Office address, City, Bangladesh',
            'phone' => '+880 1XXX-XXXXXX',
            'email' => 'info@example.org',
            'hero_title' => 'Together, we build a stronger community',
            'hero_subtitle' => 'Notices, events and the people who lead our organization.',
            'established' => '2000',
            'registration_no' => '',
            'footer_text' => '© 2026 Organization Name. All rights reserved.',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
