<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('updateMenu mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $updatingMenu = Menu::factory()->for($tenant)->create();

    $updatingMenuSection = MenuSection::factory()->for($tenant)->for($updatingMenu)->create();
    $deletingMenuSection = MenuSection::factory()->for($tenant)->for($updatingMenu)->create();

    $updatingMenuItem = MenuItem::factory()->for($tenant)->for($updatingMenuSection)->create();
    $deletingMenuItem = MenuItem::factory()->for($tenant)->for($updatingMenuSection)->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            updateMenu(
                id: "{$updatingMenu->id}"
                name: "メニュー"
                menuSections: [
                    {
                        id: "{$updatingMenuSection->id}"
                        name: "ドリンク"
                        menuItems: [
                            {
                                id: "{$updatingMenuItem->id}"
                                name: "烏龍茶"
                                price: 200
                            },
                            {
                                name: "コーラ"
                                price: 250
                            },
                        ]
                    },
                    {
                        name: "肉料理"
                        menuItems: [
                            {
                                name: "ステーキ"
                                price: 1200
                            },
                        ]
                    }
                ]
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
        ->toHaveKey('data.updateMenu')
        ->data->updateMenu->id->toBe($updatingMenu->id)
        ->data->updateMenu->name->toBe('メニュー')
        ->data->updateMenu->menuSections->toHaveCount(2)
        ->data->updateMenu->menuSections->{0}->id->toBe($updatingMenuSection->id)
        ->data->updateMenu->menuSections->{0}->name->toBe('ドリンク')
        ->data->updateMenu->menuSections->{0}->menuItems->toHaveCount(2)
        ->data->updateMenu->menuSections->{0}->menuItems->{0}->id->toBe($updatingMenuItem->id)
        ->data->updateMenu->menuSections->{0}->menuItems->{0}->name->toBe('烏龍茶')
        ->data->updateMenu->menuSections->{0}->menuItems->{0}->price->toBe(200)
        ->data->updateMenu->menuSections->{0}->menuItems->{1}->id->toBeString()
        ->data->updateMenu->menuSections->{0}->menuItems->{1}->name->toBe('コーラ')
        ->data->updateMenu->menuSections->{0}->menuItems->{1}->price->toBe(250)
        ->data->updateMenu->menuSections->{1}->id->toBeString()
        ->data->updateMenu->menuSections->{1}->name->toBe('肉料理')
        ->data->updateMenu->menuSections->{1}->menuItems->toHaveCount(1)
        ->data->updateMenu->menuSections->{1}->menuItems->{0}->id->toBeString()
        ->data->updateMenu->menuSections->{1}->menuItems->{0}->name->toBe('ステーキ')
        ->data->updateMenu->menuSections->{1}->menuItems->{0}->price->toBe(1200);

    expect($updatingMenu->fresh()->load('menuSections.menuItems'))
        ->id->toBeString()
        ->name->toBe('メニュー')
        ->menuSections->toHaveCount(2)
        ->menuSections->get(0)->id->toBe($updatingMenuSection->id)
        ->menuSections->get(0)->name->toBe('ドリンク')
        ->menuSections->get(0)->menuItems->toHaveCount(2)
        ->menuSections->get(0)->menuItems->get(0)->id->toBe($updatingMenuItem->id)
        ->menuSections->get(0)->menuItems->get(0)->name->toBe('烏龍茶')
        ->menuSections->get(0)->menuItems->get(0)->price->toBe(200)
        ->menuSections->get(0)->menuItems->get(1)->id->toBeString()
        ->menuSections->get(0)->menuItems->get(1)->name->toBe('コーラ')
        ->menuSections->get(0)->menuItems->get(1)->price->toBe(250)
        ->menuSections->get(1)->id->toBeString()
        ->menuSections->get(1)->name->toBe('肉料理')
        ->menuSections->get(1)->menuItems->toHaveCount(1)
        ->menuSections->get(1)->menuItems->get(0)->id->toBeString()
        ->menuSections->get(1)->menuItems->get(0)->name->toBe('ステーキ')
        ->menuSections->get(1)->menuItems->get(0)->price->toBe(1200)
        ->and($deletingMenuSection->fresh())->toBeNull()
        ->and($deletingMenuItem->fresh())->toBeNull();
});
