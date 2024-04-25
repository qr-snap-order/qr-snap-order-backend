<?php

use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('updateShopGroup mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $shop = Shop::factory()->for($tenant)->create();

    $shopGroup = ShopGroup::factory()
        ->for($tenant)
        ->hasAttached($shop, ['tenant_id' => $tenant->id])
        ->create();

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
            updateShopGroup (
                id: "{$shopGroup->id}"
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
        ->toHaveKey('data.updateShopGroup')
        ->data->updateShopGroup->id->toBe($shopGroup->id)
        ->data->updateShopGroup->name->toBe('関東店舗グループ')
        ->data->updateShopGroup->shops->toHaveCount(2)
        ->data->updateShopGroup->shops->{0}->name->toBe('東京支店')
        ->data->updateShopGroup->shops->{1}->name->toBe('千葉支店');

    expect($shopGroup->fresh())
        ->name->toBe('関東店舗グループ')
        ->shops->toHaveCount(2)
        ->shops->get(0)->name->toBe('東京支店')
        ->shops->get(1)->name->toBe('千葉支店');

    expect($shop->fresh())->not->toBeNull(); // disconnectするだけで、Shopは削除されてないことを確認
});
