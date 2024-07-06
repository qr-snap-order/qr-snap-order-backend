<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuItemGroup;
use App\Models\MenuSection;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('deleteMenuItemGroup mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $menu = Menu::factory()->for($tenant)->create();
    $menuSection = MenuSection::factory()->for($tenant)->for($menu)->create();
    $menuItem = MenuItem::factory()->for($tenant)->for($menuSection)->create();

    $menuItemGroup = MenuItemGroup::factory()
        ->for($tenant)
        ->hasAttached($menuItem, ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            deleteMenuItemGroup (
                id: "{$menuItemGroup->id}"

            ) {
                id
            }
        }
        GRAPHQL
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.deleteMenuItemGroup')
        ->data->deleteMenuItemGroup->id->toBe($menuItemGroup->id);

    expect($menuItemGroup->fresh())->toBeNull();

    expect($menuItem->fresh())->not->toBeNull(); // disconnectするだけで、MenuItemは削除されてないことを確認
});
