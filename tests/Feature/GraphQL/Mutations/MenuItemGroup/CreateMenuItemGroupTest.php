<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemGroup;
use App\Models\MenuSection;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('createMenuItemGroup mutation', function () {
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

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            createMenuItemGroup (
                name: "夏季限定メニュー"
                menuItems: ["{$menuItems[0]->id}", "{$menuItems[1]->id}"]

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
        ->toHaveKey('data.createMenuItemGroup')
        ->data->createMenuItemGroup->name->toBe('夏季限定メニュー')
        ->data->createMenuItemGroup->menuItems->toHaveCount(2)
        ->data->createMenuItemGroup->menuItems->{0}->name->toBe('かき氷')
        ->data->createMenuItemGroup->menuItems->{1}->name->toBe('冷やし中華');

    expect(MenuItemGroup::findOrFail($response->json('data.createMenuItemGroup.id')))
        ->name->toBe('夏季限定メニュー')
        ->menuItems->toHaveCount(2)
        ->menuItems->get(0)->name->toBe('かき氷')
        ->menuItems->get(1)->name->toBe('冷やし中華');
});
