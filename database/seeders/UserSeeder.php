<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRoleEnum;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 2 admin
        User::factory()->create([
            'name' => 'Admin One',
            'email' => 'admin1@test.com',
            'role' => UserRoleEnum::ADMIN->value,
        ]);

        User::factory()->create([
            'name' => 'Admin Two',
            'email' => 'admin2@test.com',
            'role' => UserRoleEnum::ADMIN->value,
        ]);

        // 10 customer
        User::factory()->count(10)->create([
            'role' => UserRoleEnum::CUSTOMER->value,
        ]);
    }
}
