<?php

use App\Models\Employee;
use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('createShop mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $employees = Employee::factory()
        ->forEachSequence(
            ['name' => '田中'],
            ['name' => '佐藤'],
        )
        ->for($tenant)
        ->create();

    $shopGroups = ShopGroup::factory()
        ->forEachSequence(
            ['name' => '関東地区'],
            ['name' => '関西地区'],
        )
        ->for($tenant)
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(/** @lang GraphQL */ <<<GRAPHQL
        mutation {
            createShop (
                name: "東京支店"
                employees: ["{$employees[0]->id}", "{$employees[1]->id}"]
                shopGroups: ["{$shopGroups[0]->id}", "{$shopGroups[1]->id}"]
            ) {
                id
                name
                employees {
                    id
                    name
                }
                shopGroups {
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
        ->toHaveKey('data.createShop')
        ->data->createShop->name->toBe('東京支店')
        ->data->createShop->employees->toHaveCount(2)
        ->data->createShop->employees->{0}->name->toBe('田中')
        ->data->createShop->employees->{1}->name->toBe('佐藤')
        ->data->createShop->shopGroups->toHaveCount(2)
        ->data->createShop->shopGroups->{0}->name->toBe('関東地区')
        ->data->createShop->shopGroups->{1}->name->toBe('関西地区');

    expect(Shop::findOrFail($response->json('data.createShop.id')))
        ->name->toBe('東京支店')
        ->employees->toHaveCount(2)
        ->employees->get(0)->name->toBe('田中')
        ->employees->get(1)->name->toBe('佐藤')
        ->shopGroups->get(0)->name->toBe('関東地区')
        ->shopGroups->get(1)->name->toBe('関西地区');
});
