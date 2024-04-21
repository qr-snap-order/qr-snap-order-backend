<?php

use App\Models\Menu;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('createMenu mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */
        'mutation {
            createMenu(
                name: "メニュー"
                menuSections: [
                    {
                        name: "ドリンク"
                        menuItems: [
                            {
                                name: "烏龍茶"
                                price: 200
                            },
                            {
                                name: "コーラ"
                                price: 250
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
        }'
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.createMenu')
        ->data->createMenu->id->toBeString()
        ->data->createMenu->name->toBe('メニュー')
        ->data->createMenu->menuSections->toHaveCount(1)
        ->data->createMenu->menuSections->{0}->id->toBeString()
        ->data->createMenu->menuSections->{0}->name->toBe('ドリンク')
        ->data->createMenu->menuSections->{0}->menuItems->toHaveCount(2)
        ->data->createMenu->menuSections->{0}->menuItems->{0}->id->toBeString()
        ->data->createMenu->menuSections->{0}->menuItems->{0}->name->toBe('烏龍茶')
        ->data->createMenu->menuSections->{0}->menuItems->{0}->price->toBe(200)
        ->data->createMenu->menuSections->{0}->menuItems->{1}->id->toBeString()
        ->data->createMenu->menuSections->{0}->menuItems->{1}->name->toBe('コーラ')
        ->data->createMenu->menuSections->{0}->menuItems->{1}->price->toBe(250);

    expect(Menu::findOrFail($response->json('data.createMenu.id')))
        ->id->toBeString()
        ->name->toBe('メニュー')
        ->menuSections->toHaveCount(1)
        ->menuSections->get(0)->id->toBeString()
        ->menuSections->get(0)->name->toBe('ドリンク')
        ->menuSections->get(0)->menuItems->toHaveCount(2)
        ->menuSections->get(0)->menuItems->get(0)->id->toBeString()
        ->menuSections->get(0)->menuItems->get(0)->name->toBe('烏龍茶')
        ->menuSections->get(0)->menuItems->get(0)->price->toBe(200)
        ->menuSections->get(0)->menuItems->get(1)->id->toBeString()
        ->menuSections->get(0)->menuItems->get(1)->name->toBe('コーラ')
        ->menuSections->get(0)->menuItems->get(1)->price->toBe(250);
});
