<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin User',
                'email'    => 'admin@example.com',
                'status'   => UserStatusEnum::Active,
                'role'     => UserRoleEnum::Admin,
                'password' => bcrypt('password'),
            ]
        );

        // Create sample users (mix of admin and merchant roles)
        User::factory()->admin()->count(3)->create();
        User::factory()->merchant()->count(10)->create();
        User::factory()->count(12)->create(); // random statuses/roles

        // Create merchants with form fields and sample submissions
        $this->call(ZidMerchantSeeder::class);
    }
}
