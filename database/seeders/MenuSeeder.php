<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::findOrFail('00000000-0000-0000-0000-000000000000');

        Menu::factory([
            'id' => '00000000-0000-0000-0000-000000000000',
        ])
            ->for($tenant)
            ->has(
                MenuSection::factory(2)->for($tenant)
                    ->has(
                        MenuItem::factory(2)->for($tenant)
                    )
            )
            ->create();
    }
}
