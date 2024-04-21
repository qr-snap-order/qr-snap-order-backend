<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::findOrFail('00000000-0000-0000-0000-000000000000');

        User::factory([
            'id' => '00000000-0000-0000-0000-000000000000',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ])->for($tenant)->create();
    }
}
