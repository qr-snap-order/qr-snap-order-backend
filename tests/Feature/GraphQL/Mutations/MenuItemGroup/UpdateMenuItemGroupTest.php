<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemGroup;
use App\Models\MenuSection;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('updateMenuItemGroup mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $menu = Menu::factory()->for($tenant)->create();
    $menuSection = MenuSection::factory()->for($tenant)->for($menu)->create();
    $menuItems = MenuItem::factory()
        ->forEachSequence(
            ['name' => 'かき氷'],
            ['name' => '冷やし中華'],
            ['name' => 'そうめん'],
        )
        ->for($tenant)
        ->for($menuSection)
        ->create();

    $menuItemGroup = MenuItemGroup::factory()
        ->for($tenant)
        ->hasAttached($menuItems[0], ['tenant_id' => $tenant->id])
        ->hasAttached($menuItems[1], ['tenant_id' => $tenant->id])
        ->create();


    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            updateMenuItemGroup (
                id: "{$menuItemGroup->id}"
                name: "夏季限定メニュー"
                menuItems: ["{$menuItems[1]->id}", "{$menuItems[2]->id}"]

            ) {
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
        ->toHaveKey('data.updateMenuItemGroup')
        ->data->updateMenuItemGroup->id->toBe($menuItemGroup->id)
        ->data->updateMenuItemGroup->name->toBe('夏季限定メニュー')
        ->data->updateMenuItemGroup->menuItems->toHaveCount(2)
        ->data->updateMenuItemGroup->menuItems->{0}->name->toBe('冷やし中華')
        ->data->updateMenuItemGroup->menuItems->{1}->name->toBe('そうめん');

    expect($menuItemGroup->fresh())
        ->name->toBe('夏季限定メニュー')
        ->menuItems->toHaveCount(2)
        ->menuItems->get(0)->name->toBe('冷やし中華')
        ->menuItems->get(1)->name->toBe('そうめん');

    expect($menuItems[0]->fresh())->not->toBeNull(); // disconnectするだけで、MenuItemは削除されてないことを確認
});
