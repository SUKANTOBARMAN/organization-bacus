<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => config('site.admin_email')],
            [
                'name' => config('site.admin_name'),
                'password' => config('site.admin_password'),
                'role' => 'super_admin',
                'is_active' => true,
            ],
        );
    }
}
