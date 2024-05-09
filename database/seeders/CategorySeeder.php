<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::findOrFail('00000000-0000-0000-0000-000000000000');

        Category::factory()
            ->for($tenant)
            ->forEachSequence(
                [
                    'id' => '00000000-0000-0000-0000-000000000000',
                    'name' => '春限定'
                ],
                [
                    'id' => '00000000-0000-0000-0000-000000000001',
                    'name' => '夏限定'
                ],
                [
                    'id' => '00000000-0000-0000-0000-000000000002',
                    'name' => '秋限定'
                ],
                [
                    'id' => '00000000-0000-0000-0000-000000000003',
                    'name' => '冬限定'
                ],
            )
            ->create();
    }
}
