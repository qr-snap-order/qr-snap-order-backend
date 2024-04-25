<?php

use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('shopGroup query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $shopGroup = ShopGroup::factory(['name' => '関東店舗グループ'])
        ->for($tenant)
        ->hasAttached(Shop::factory(['name' => '東京支店'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(Shop::factory(['name' => '千葉支店'])->for($tenant), ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        query {
            shopGroup(id: "{$shopGroup->id}") {
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
        ->toHaveKey('data.shopGroup')
        ->data->shopGroup->name->toBe('関東店舗グループ')
        ->data->shopGroup->shops->toHaveCount(2)
        ->data->shopGroup->shops->{0}->name->toBe('東京支店')
        ->data->shopGroup->shops->{1}->name->toBe('千葉支店');
});
