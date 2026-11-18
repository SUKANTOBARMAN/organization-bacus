<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::firstOrCreate(['slug' => 'about'], [
            'title' => 'About Us',
            'body' => '<p>সংগঠনের পরিচিতি এখানে লিখুন।</p>',
        ]);
    }
}
