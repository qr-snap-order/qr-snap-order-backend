<?php

use App\Models\Employee;
use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('updateShop mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $employees = Employee::factory()->forEachSequence(
        ['name' => '山田'],
        ['name' => '田中'],
        ['name' => '佐藤'],
    )->for($tenant)->create();

    $shopGroups = ShopGroup::factory()
        ->forEachSequence(
            ['name' => '関東地区'],
            ['name' => '関西地区'],
            ['name' => '九州地区'],
        )
        ->for($tenant)
        ->create();

    $shop = Shop::factory()
        ->for($tenant)
        ->hasAttached($employees[0], ['tenant_id' => $tenant->id])
        ->hasAttached($employees[1], ['tenant_id' => $tenant->id])
        ->hasAttached($shopGroups[0], ['tenant_id' => $tenant->id])
        ->hasAttached($shopGroups[1], ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            updateShop (
                id: "{$shop->id}"
                name: "東京支店"
                employees: ["{$employees[1]->id}", "{$employees[2]->id}"]
                shopGroups: ["{$shopGroups[1]->id}", "{$shopGroups[2]->id}"]
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
        ->toHaveKey('data.updateShop')
        ->data->updateShop->id->toBe($shop->id)
        ->data->updateShop->name->toBe('東京支店')
        ->data->updateShop->employees->toHaveCount(2)
        ->data->updateShop->employees->{0}->name->toBe('田中')
        ->data->updateShop->employees->{1}->name->toBe('佐藤')
        ->data->updateShop->shopGroups->{0}->name->toBe('関西地区')
        ->data->updateShop->shopGroups->{1}->name->toBe('九州地区');

    expect($shop->fresh())
        ->name->toBe('東京支店')
        ->employees->toHaveCount(2)
        ->employees->get(0)->name->toBe('田中')
        ->employees->get(1)->name->toBe('佐藤')
        ->shopGroups->get(0)->name->toBe('関西地区')
        ->shopGroups->get(1)->name->toBe('九州地区');

    expect($employees[0]->fresh())->not->toBeNull(); // disconnectするだけで、Employeeは削除されてないことを確認
    expect($shopGroups[0]->fresh())->not->toBeNull(); // disconnectするだけで、shopGroupeは削除されてないことを確認
});
