<?php

use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('deleteShopGroup mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $shop = Shop::factory()->for($tenant)->create();

    $shopGroup = ShopGroup::factory()
        ->for($tenant)
        ->hasAttached($shop, ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            deleteShopGroup (
                id: "{$shopGroup->id}"

            ) {
                id
            }
        }
        GRAPHQL
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.deleteShopGroup')
        ->data->deleteShopGroup->id->toBe($shopGroup->id);

    expect($shopGroup->fresh())->toBeNull();

    expect($shop->fresh())->not->toBeNull(); // disconnectするだけで、Shopは削除されてないことを確認
});
