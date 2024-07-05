<?php

use App\Models\Employee;
use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('deleteShop mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $employee = Employee::factory()->for($tenant)->create();

    $shopGroup = ShopGroup::factory()->for($tenant)->create();

    $shop = Shop::factory()
        ->for($tenant)
        ->hasAttached($employee, ['tenant_id' => $tenant->id])
        ->hasAttached($shopGroup, ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            deleteShop (
                id: "{$shop->id}"

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
        ->toHaveKey('data.deleteShop')
        ->data->deleteShop->id->toBe($shop->id);

    expect($shop->fresh())->toBeNull();

    expect($employee->fresh())->not->toBeNull(); // disconnectするだけで、Employeeは削除されてないことを確認
    expect($shopGroup->fresh())->not->toBeNull(); // disconnectするだけで、ShopGroupは削除されてないことを確認
});
