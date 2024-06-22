<?php

use App\Models\Category;
use App\Models\Menu;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

test('createMenu mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $category = Category::factory()->for($tenant)->create();

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
                                        categories: ["{$category->id}"]
                                    },
                                    {
                                        name: "コーラ"
                                        price: 250
                                        categories: []
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
                                categories {
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
        ->data->createMenu->menuSections->{0}->menuItems->{0}->image->toMatch('/^menu-item\/image\/\w{40}\.png$/')
        ->data->createMenu->menuSections->{0}->menuItems->{0}->categories->toHaveCount(1)
        ->data->createMenu->menuSections->{0}->menuItems->{0}->categories->{0}->id->toBe($category->id)
        ->data->createMenu->menuSections->{0}->menuItems->{1}->id->toBeString()
        ->data->createMenu->menuSections->{0}->menuItems->{1}->name->toBe('コーラ')
        ->data->createMenu->menuSections->{0}->menuItems->{1}->price->toBe(250)
        ->data->createMenu->menuSections->{0}->menuItems->{1}->image->toBeNull()
        ->data->createMenu->menuSections->{0}->menuItems->{1}->categories->toHaveCount(0);

    expect(Menu::with(['menuSections.menuItems.categories'])->findOrFail($response->json('data.createMenu.id')))
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
        ->menuSections->get(0)->menuItems->get(0)->categories->toHaveCount(1)
        ->menuSections->get(0)->menuItems->get(0)->categories->get(0)->id->toBe($category->id)
        ->menuSections->get(0)->menuItems->get(1)->id->toBeString()
        ->menuSections->get(0)->menuItems->get(1)->name->toBe('コーラ')
        ->menuSections->get(0)->menuItems->get(1)->price->toBe(250)
        ->menuSections->get(0)->menuItems->get(1)->image->toBeNull()
        ->menuSections->get(0)->menuItems->get(1)->categories->toHaveCount(0);
});
