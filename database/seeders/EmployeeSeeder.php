<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Shop;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organization = Organization::findOrFail('00000000-0000-0000-0000-000000000000');

        $shop = Shop::findOrFail('00000000-0000-0000-0000-000000000000');

        Employee::factory()
            ->for($organization)
            ->hasAttached($shop)
            ->forEachSequence(
                [
                    'id' => '00000000-0000-0000-0000-000000000000',
                    'name' => '山田太郎',
                ],
                [
                    'id' => '00000000-0000-0000-0000-000000000001',
                    'name' => '鈴木花子',
                ],
            )
            ->create();
    }
}
