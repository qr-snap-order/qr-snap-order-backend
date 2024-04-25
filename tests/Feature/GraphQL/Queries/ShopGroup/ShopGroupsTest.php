<?php

use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('shopGroups query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    ShopGroup::factory(['name' => '関東店舗グループ'])
        ->for($tenant)
        ->hasAttached(Shop::factory(['name' => '東京支店'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(Shop::factory(['name' => '千葉支店'])->for($tenant), ['tenant_id' => $tenant->id])
        ->create();

    ShopGroup::factory(['name' => '九州店舗グループ'])
        ->for($tenant)
        ->hasAttached(Shop::factory(['name' => '福岡支店'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(Shop::factory(['name' => '佐賀支店'])->for($tenant), ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        query {
            shopGroups {
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
        ->toHaveKey('data.shopGroups')
        ->data->shopGroups->{0}->name->toBe('関東店舗グループ')
        ->data->shopGroups->{0}->shops->toHaveCount(2)
        ->data->shopGroups->{0}->shops->{0}->name->toBe('東京支店')
        ->data->shopGroups->{0}->shops->{1}->name->toBe('千葉支店')
        ->data->shopGroups->{1}->name->toBe('九州店舗グループ')
        ->data->shopGroups->{1}->shops->toHaveCount(2)
        ->data->shopGroups->{1}->shops->{0}->name->toBe('福岡支店')
        ->data->shopGroups->{1}->shops->{1}->name->toBe('佐賀支店');
});
