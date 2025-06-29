<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\ShopTable;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::findOrFail('00000000-0000-0000-0000-000000000000');

        $shops = Shop::factory()
            ->for($tenant)
            ->forEachSequence(
                [
                    'id' => '00000000-0000-0000-0000-000000000000',
                    'name' => '東京支店',
                ],
                [
                    'id' => '00000000-0000-0000-0000-000000000001',
                    'name' => '大阪支店',
                ],
            )
            ->create();

        ShopTable::factory()
            ->for($tenant)
            ->for($shops[0])
            ->forEachSequence(
                [
                    'name' => 'テーブルA',
                ],
                [
                    'name' => 'テーブルB',
                ],
                [
                    'name' => 'テーブルC',
                ],
            )
            ->create();

        ShopTable::factory()
            ->for($tenant)
            ->for($shops[1])
            ->forEachSequence(
                [
                    'name' => 'テーブルA',
                ],
                [
                    'name' => 'テーブルB',
                ],
                [
                    'name' => 'テーブルC',
                ],
            )
            ->create();
    }
}
