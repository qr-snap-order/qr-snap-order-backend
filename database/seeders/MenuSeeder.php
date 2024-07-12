<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemGroup;
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

        $groups = MenuItemGroup::factory()
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

        $menu = Menu::factory([
            'id' => '00000000-0000-0000-0000-000000000000',
        ])
            ->for($tenant)
            ->create();

        $sections = MenuSection::factory()
            ->for($tenant)
            ->for($menu)
            ->forEachSequence(
                [
                    'name' => '季節の味',
                    'sort_key' => 1
                ],
                [
                    'name' => '肉料理',
                    'sort_key' => 2
                ],
                [
                    'name' => 'ドリンク',
                    'sort_key' => 2
                ],
            )
            ->create();

        MenuItem::factory()
            ->for($tenant)
            ->for($sections[0]) // 季節の味
            ->hasAttached($groups[0], ['tenant_id' => $tenant->id]) // 春限定
            ->forEachSequence(
                [
                    'name' => '桜餅',
                    'sort_key' => 1
                ],
                [
                    'name' => 'さくらんぼ',
                    'sort_key' => 2
                ],
            )
            ->create();

        MenuItem::factory()
            ->for($tenant)
            ->for($sections[0]) // 季節の味
            ->hasAttached($groups[1], ['tenant_id' => $tenant->id]) // 夏限定
            ->forEachSequence(
                [
                    'name' => '冷やし中華',
                    'sort_key' => 1
                ],
                [
                    'name' => 'かき氷',
                    'sort_key' => 2
                ],
            )
            ->create();

        MenuItem::factory()
            ->for($tenant)
            ->for($sections[0]) // 季節の味
            ->hasAttached($groups[2], ['tenant_id' => $tenant->id]) // 秋限定
            ->forEachSequence(
                [
                    'name' => '松茸',
                    'sort_key' => 1
                ],
                [
                    'name' => '秋刀魚',
                    'sort_key' => 2
                ],
            )
            ->create();

        MenuItem::factory()
            ->for($tenant)
            ->for($sections[0]) // 季節の味
            ->hasAttached($groups[3], ['tenant_id' => $tenant->id]) // 冬限定
            ->forEachSequence(
                [
                    'name' => '牡蠣',
                    'sort_key' => 1
                ],
                [
                    'name' => 'ぶり',
                    'sort_key' => 2
                ],
            )
            ->create();

        MenuItem::factory()
            ->for($tenant)
            ->for($sections[1]) // 肉料理
            ->forEachSequence(
                [
                    'name' => 'ステーキ',
                    'sort_key' => 1
                ],
                [
                    'name' => '唐揚げ',
                    'sort_key' => 2
                ],
            )
            ->create();

        MenuItem::factory()
            ->for($tenant)
            ->for($sections[2]) // ドリンク
            ->forEachSequence(
                [
                    'name' => 'ビール',
                    'sort_key' => 1
                ],
                [
                    'name' => 'ワイン',
                    'sort_key' => 2
                ],
            )
            ->create();
    }
}
