<?php

use App\Models\Employee;
use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('shop query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $shop = Shop::factory(['name' => '東京支店'])
        ->hasAttached(Employee::factory(['name' => '田中'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(Employee::factory(['name' => '佐藤'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(ShopGroup::factory(['name' => '関東地区'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(ShopGroup::factory(['name' => '直営店舗'])->for($tenant), ['tenant_id' => $tenant->id])
        ->for($tenant)->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        query {
            shop(id: "{$shop->id}") {
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
        ->toHaveKey('data.shop')
        ->data->shop->name->toBe('東京支店')
        ->data->shop->employees->toHaveCount(2)
        ->data->shop->employees->{0}->name->toBe('田中')
        ->data->shop->employees->{1}->name->toBe('佐藤')
        ->data->shop->shopGroups->{0}->name->toBe('関東地区')
        ->data->shop->shopGroups->{1}->name->toBe('直営店舗');
});
