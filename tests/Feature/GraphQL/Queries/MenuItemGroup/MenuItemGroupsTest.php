<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemGroup;
use App\Models\MenuSection;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('menuItemGroups query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $menu = Menu::factory()->for($tenant)->create();
    $menuSection = MenuSection::factory()->for($tenant)->for($menu)->create();
    $menuItems = MenuItem::factory()
        ->forEachSequence(
            ['name' => 'かき氷'],
            ['name' => '冷やし中華'],
            ['name' => 'おでん'],
            ['name' => 'ぜんざい'],
        )
        ->for($tenant)
        ->for($menuSection)
        ->create();

    MenuItemGroup::factory(['name' => '夏限定メニュー'])
        ->for($tenant)
        ->hasAttached($menuItems[0], ['tenant_id' => $tenant->id])
        ->hasAttached($menuItems[1], ['tenant_id' => $tenant->id])
        ->create();

    MenuItemGroup::factory(['name' => '冬限定メニュー'])
        ->for($tenant)
        ->hasAttached($menuItems[2], ['tenant_id' => $tenant->id])
        ->hasAttached($menuItems[3], ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        query {
            menuItemGroups {
                id
                name
                menuItems {
                    id
                    name
                }
            }
        }
        GRAPHQL
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.menuItemGroups')
        ->data->menuItemGroups->{0}->name->toBe('夏限定メニュー')
        ->data->menuItemGroups->{0}->menuItems->toHaveCount(2)
        ->data->menuItemGroups->{0}->menuItems->{0}->name->toBe('かき氷')
        ->data->menuItemGroups->{0}->menuItems->{1}->name->toBe('冷やし中華')
        ->data->menuItemGroups->{1}->name->toBe('冬限定メニュー')
        ->data->menuItemGroups->{1}->menuItems->toHaveCount(2)
        ->data->menuItemGroups->{1}->menuItems->{0}->name->toBe('おでん')
        ->data->menuItemGroups->{1}->menuItems->{1}->name->toBe('ぜんざい');
});
