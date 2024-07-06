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

    $shops = Shop::factory()
        ->forEachSequence(
            ['name' => '東京支店'],
            ['name' => '千葉支店'],
            ['name' => '神奈川支店'],
        )
        ->for($tenant)
        ->create();

    $shopGroup = ShopGroup::factory()
        ->for($tenant)
        ->hasAttached($shops[0], ['tenant_id' => $tenant->id])
        ->hasAttached($shops[1], ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            updateShopGroup (
                id: "{$shopGroup->id}"
                name: "関東店舗グループ"
                shops: ["{$shops[1]->id}", "{$shops[2]->id}"]

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
        ->data->updateShopGroup->shops->{0}->name->toBe('千葉支店')
        ->data->updateShopGroup->shops->{1}->name->toBe('神奈川支店');

    expect($shopGroup->fresh())
        ->name->toBe('関東店舗グループ')
        ->shops->toHaveCount(2)
        ->shops->get(0)->name->toBe('千葉支店')
        ->shops->get(1)->name->toBe('神奈川支店');

    expect($shops[0]->fresh())->not->toBeNull(); // disconnectするだけで、Shopは削除されてないことを確認
});