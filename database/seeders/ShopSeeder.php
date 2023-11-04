<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Shop;
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
        $organization = Organization::findOrFail('00000000-0000-0000-0000-000000000000');

        Shop::factory()
            ->for($organization)
            ->forEachSequence(
                [
                    'id' => '00000000-0000-0000-0000-000000000000',
                ],
                [
                    'id' => '00000000-0000-0000-0000-000000000001',
                ],
            )
            ->create();
    }
}
