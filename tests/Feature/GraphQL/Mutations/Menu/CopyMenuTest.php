<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('copyMenu mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $menu = Menu::factory([
        'name' => 'メニュー',
    ])
        ->for($tenant)
        ->has(
            MenuSection::factory([
                'name' => 'ドリンク',
            ])
                ->for($tenant)
                ->has(
                    MenuItem::factory([
                        'name' => '烏龍茶',
                        'price' => 200,
                    ])
                        ->for($tenant)
                )
        )
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            copyMenu(
                id: "{$menu->id}"
            ) {
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
        ->toHaveKey('data.copyMenu')
        ->data->copyMenu->id->toBeString()
        ->data->copyMenu->id->not->toBe($menu->id)
        ->data->copyMenu->name->toBe('メニュー')
        ->data->copyMenu->menuSections->toHaveCount(1)
        ->data->copyMenu->menuSections->{0}->id->toBeString()
        ->data->copyMenu->menuSections->{0}->name->toBe('ドリンク')
        ->data->copyMenu->menuSections->{0}->menuItems->toHaveCount(1)
        ->data->copyMenu->menuSections->{0}->menuItems->{0}->id->toBeString()
        ->data->copyMenu->menuSections->{0}->menuItems->{0}->name->toBe('烏龍茶')
        ->data->copyMenu->menuSections->{0}->menuItems->{0}->price->toBe(200);

    expect(Menu::findOrFail($response->json('data.copyMenu.id')))
        ->id->toBeString()
        ->name->toBe('メニュー')
        ->menuSections->toHaveCount(1)
        ->menuSections->get(0)->id->toBeString()
        ->menuSections->get(0)->name->toBe('ドリンク')
        ->menuSections->get(0)->menuItems->toHaveCount(1)
        ->menuSections->get(0)->menuItems->get(0)->id->toBeString()
        ->menuSections->get(0)->menuItems->get(0)->name->toBe('烏龍茶')
        ->menuSections->get(0)->menuItems->get(0)->price->toBe(200);
});
