<?php

use App\Models\Employee;
use App\Models\Shop;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('updateShop mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $employee = Employee::factory()->for($tenant)->create();

    $shop = Shop::factory()
        ->for($tenant)
        ->hasAttached($employee, ['tenant_id' => $tenant->id])
        ->create();

    $connectingEmployees = Employee::factory()
        ->forEachSequence(
            ['name' => '田中'],
            ['name' => '佐藤'],
        )
        ->for($tenant)
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            updateShop (
                id: "{$shop->id}"
                name: "東京支店"
                employees: {
                    sync: ["{$connectingEmployees[0]->id}", "{$connectingEmployees[1]->id}"]
                }
            ) {
                id
                name
                employees {
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
        ->data->updateShop->employees->{1}->name->toBe('佐藤');

    expect($shop->fresh())
        ->name->toBe('東京支店')
        ->employees->toHaveCount(2)
        ->employees->get(0)->name->toBe('田中')
        ->employees->get(1)->name->toBe('佐藤');

    expect($employee->fresh())->not->toBeNull(); // disconnectするだけで、Employeeは削除されてないことを確認
});
