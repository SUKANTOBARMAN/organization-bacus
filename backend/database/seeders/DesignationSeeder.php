<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;

/** পদবি ও র‍্যাঙ্ক: ছোট সংখ্যা = তালিকায় আগে। প্রয়োজনমতো অ্যাডমিন প্যানেল থেকে বদলানো যায়। */
class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['Chief Patron', 'প্রধান পৃষ্ঠপোষক', 1, false],
            ['Advisor', 'উপদেষ্টা', 2, false],
            ['President', 'সভাপতি', 10, true],
            ['Vice President', 'সহ-সভাপতি', 20, true],
            ['General Secretary', 'সাধারণ সম্পাদক', 30, true],
            ['Joint Secretary', 'যুগ্ম সাধারণ সম্পাদক', 40, true],
            ['Treasurer', 'কোষাধ্যক্ষ', 50, true],
            ['Organizing Secretary', 'সাংগঠনিক সম্পাদক', 60, true],
            ['Office Secretary', 'দপ্তর সম্পাদক', 70, true],
            ['Executive Member', 'কার্যনির্বাহী সদস্য', 100, true],
            ['Life Member', 'আজীবন সদস্য', 200, false],
            ['General Member', 'সাধারণ সদস্য', 300, false],
        ];

        foreach ($rows as [$name, $bn, $rank, $exec]) {
            Designation::firstOrCreate(['name' => $name], ['name_bn' => $bn, 'rank' => $rank, 'is_executive' => $exec]);
        }
    }
}
