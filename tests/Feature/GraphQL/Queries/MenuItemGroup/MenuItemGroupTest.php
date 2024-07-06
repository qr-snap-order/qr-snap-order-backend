<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemGroup;
use App\Models\MenuSection;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('menuItemGroup query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $menu = Menu::factory()->for($tenant)->create();
    $menuSection = MenuSection::factory()->for($tenant)->for($menu)->create();
    $menuItems = MenuItem::factory()
        ->forEachSequence(
            ['name' => 'かき氷'],
            ['name' => '冷やし中華'],
        )
        ->for($tenant)
        ->for($menuSection)
        ->create();

    $menuItemGroup = MenuItemGroup::factory(['name' => '夏季限定メニュー'])
        ->for($tenant)
        ->hasAttached($menuItems[0], ['tenant_id' => $tenant->id])
        ->hasAttached($menuItems[1], ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        query {
            menuItemGroup(id: "{$menuItemGroup->id}") {
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
        ->toHaveKey('data.menuItemGroup')
        ->data->menuItemGroup->name->toBe('夏季限定メニュー')
        ->data->menuItemGroup->menuItems->toHaveCount(2)
        ->data->menuItemGroup->menuItems->{0}->name->toBe('かき氷')
        ->data->menuItemGroup->menuItems->{1}->name->toBe('冷やし中華');
});
