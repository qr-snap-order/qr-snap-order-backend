<?php

use App\Models\Menu;
use App\Models\MenuItemGroup;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

test('createMenu mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $menuItemGroup = MenuItemGroup::factory()->for($tenant)->create();

    $response = $this->domain($tenant)->actingAs($user)->multipartGraphQL(
        [
            'query' =>
            /** @lang GraphQL */
            <<<GRAPHQL
                mutation (\$image: Upload!) {
                    createMenu(
                        name: "メニュー"
                        menuSections: [
                            {
                                name: "ドリンク"
                                menuItems: [
                                    {
                                        name: "烏龍茶"
                                        price: 200
                                        image: \$image
                                        menuItemGroups: ["{$menuItemGroup->id}"]
                                    },
                                    {
                                        name: "コーラ"
                                        price: 250
                                        menuItemGroups: []
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
                                image
                                menuItemGroups {
                                    id
                                }
                            }
                        }
                    }
                }
            GRAPHQL,
            'variables' => [
                'image' => null,
            ],
        ],
        [
            '0' => ['variables.image'],
        ],
        [
            '0' => UploadedFile::fake()->create('image.png', 500),
        ]
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
        ->data->createMenu->menuSections->{0}->menuItems->{0}->image->toMatch('/\/menu-item\/image\/\w{40}\.png$/') // image url
        ->data->createMenu->menuSections->{0}->menuItems->{0}->menuItemGroups->toHaveCount(1)
        ->data->createMenu->menuSections->{0}->menuItems->{0}->menuItemGroups->{0}->id->toBe($menuItemGroup->id)
        ->data->createMenu->menuSections->{0}->menuItems->{1}->id->toBeString()
        ->data->createMenu->menuSections->{0}->menuItems->{1}->name->toBe('コーラ')
        ->data->createMenu->menuSections->{0}->menuItems->{1}->price->toBe(250)
        ->data->createMenu->menuSections->{0}->menuItems->{1}->image->toBeNull()
        ->data->createMenu->menuSections->{0}->menuItems->{1}->menuItemGroups->toHaveCount(0);

    expect(Menu::with(['menuSections.menuItems.menuItemGroups'])->findOrFail($response->json('data.createMenu.id')))
        ->id->toBeString()
        ->name->toBe('メニュー')
        ->menuSections->toHaveCount(1)
        ->menuSections->get(0)->id->toBeString()
        ->menuSections->get(0)->name->toBe('ドリンク')
        ->menuSections->get(0)->menuItems->toHaveCount(2)
        ->menuSections->get(0)->menuItems->get(0)->id->toBeString()
        ->menuSections->get(0)->menuItems->get(0)->name->toBe('烏龍茶')
        ->menuSections->get(0)->menuItems->get(0)->price->toBe(200)
        ->menuSections->get(0)->menuItems->get(0)->image->toMatch('/^menu-item\/image\/\w{40}\.png$/')
        ->menuSections->get(0)->menuItems->get(0)->sort_key->toBe(1)
        ->menuSections->get(0)->menuItems->get(0)->menuItemGroups->toHaveCount(1)
        ->menuSections->get(0)->menuItems->get(0)->menuItemGroups->get(0)->id->toBe($menuItemGroup->id)
        ->menuSections->get(0)->menuItems->get(1)->id->toBeString()
        ->menuSections->get(0)->menuItems->get(1)->name->toBe('コーラ')
        ->menuSections->get(0)->menuItems->get(1)->price->toBe(250)
        ->menuSections->get(0)->menuItems->get(1)->image->toBeNull()
        ->menuSections->get(0)->menuItems->get(1)->sort_key->toBe(2)
        ->menuSections->get(0)->menuItems->get(1)->menuItemGroups->toHaveCount(0);
});
