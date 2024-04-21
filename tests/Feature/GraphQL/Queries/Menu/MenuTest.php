<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('menu query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $menu = Menu::factory()->for($tenant)->create();
    $menuSection = MenuSection::factory()->for($tenant)->for($menu)->create();
    $menuItem = MenuItem::factory()->for($tenant)->for($menuSection)->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        query {
            menu(id: "{$menu->id}") {
                id
                name
                menuSections {
                    id
                    name
                    menuItems {
                        id
                        name
                        price
                    }
                }
            }
        }
        GRAPHQL
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.menu')
        ->data->menu->id->toBe($menu->id)
        ->data->menu->name->toBe($menu->name)
        ->data->menu->menuSections->toHaveCount(1)
        ->data->menu->menuSections->{0}->id->toBe($menuSection->id)
        ->data->menu->menuSections->{0}->name->toBe($menuSection->name)
        ->data->menu->menuSections->{0}->menuItems->toHaveCount(1)
        ->data->menu->menuSections->{0}->menuItems->{0}->id->toBe($menuItem->id)
        ->data->menu->menuSections->{0}->menuItems->{0}->name->toBe($menuItem->name)
        ->data->menu->menuSections->{0}->menuItems->{0}->price->toBe($menuItem->price);
});
