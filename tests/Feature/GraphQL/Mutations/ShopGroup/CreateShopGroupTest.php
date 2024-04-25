<?php

use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('createShopGroup mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $connectingShops = Shop::factory()
        ->forEachSequence(
            ['name' => '東京支店'],
            ['name' => '千葉支店'],
        )
        ->for($tenant)
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            createShopGroup (
                name: "関東店舗グループ"
                shops: ["{$connectingShops[0]->id}", "{$connectingShops[1]->id}"]

            ) {
                id
                name
                shops {
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
        ->toHaveKey('data.createShopGroup')
        ->data->createShopGroup->name->toBe('関東店舗グループ')
        ->data->createShopGroup->shops->toHaveCount(2)
        ->data->createShopGroup->shops->{0}->name->toBe('東京支店')
        ->data->createShopGroup->shops->{1}->name->toBe('千葉支店');

    expect(ShopGroup::findOrFail($response->json('data.createShopGroup.id')))
        ->name->toBe('関東店舗グループ')
        ->shops->toHaveCount(2)
        ->shops->get(0)->name->toBe('東京支店')
        ->shops->get(1)->name->toBe('千葉支店');
});
