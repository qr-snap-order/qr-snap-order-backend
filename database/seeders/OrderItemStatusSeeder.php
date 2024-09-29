<?php

namespace Database\Seeders;

use App\Models\OrderItemStatus;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::findOrFail('00000000-0000-0000-0000-000000000000');

        OrderItemStatus::factory()
            ->for($tenant)
            ->forEachSequence(
                [
                    'id' => '00000000-0000-0000-0000-000000000000',
                    'name' => '受付',
                    'color' => '#FFFFFF',
                    'sort_key' => 1,
                ],
                [
                    'id' => '00000000-0000-0000-0000-000000000001',
                    'name' => '調理中',
                    'color' => '#FFFFFF',
                    'sort_key' => 2,
                ],
                [
                    'id' => '00000000-0000-0000-0000-000000000002',
                    'name' => '調理済',
                    'color' => '#FFFFFF',
                    'sort_key' => 3,
                ],
                [
                    'id' => '00000000-0000-0000-0000-000000000003',
                    'name' => '配膳中',
                    'color' => '#FFFFFF',
                    'sort_key' => 4,
                ],
                [
                    'id' => '00000000-0000-0000-0000-000000000004',
                    'name' => '配膳済',
                    'color' => '#FFFFFF',
                    'sort_key' => 5,
                ],
                [
                    'id' => '00000000-0000-0000-0000-000000000005',
                    'name' => 'キャンセル',
                    'color' => '#FFFFFF',
                    'sort_key' => 6,
                ],
            )
            ->create();
    }
}
