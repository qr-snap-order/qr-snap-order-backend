<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Shop;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::findOrFail('00000000-0000-0000-0000-000000000000');

        $shop = Shop::findOrFail('00000000-0000-0000-0000-000000000000');

        Employee::factory()
            ->for($tenant)
            ->hasAttached($shop, ['tenant_id' => '00000000-0000-0000-0000-000000000000'])
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
