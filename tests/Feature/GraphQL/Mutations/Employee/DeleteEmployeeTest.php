<?php

use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\Shop;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('deleteEmployee mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $shop = Shop::factory()->for($tenant)->create();

    $employeeGroup = EmployeeGroup::factory()->for($tenant)->create();

    $employee = Employee::factory()
        ->for($tenant)
        ->hasAttached($shop, ['tenant_id' => $tenant->id])
        ->hasAttached($employeeGroup, ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            deleteEmployee (
                id: "{$employee->id}"

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
        ->toHaveKey('data.deleteEmployee')
        ->data->deleteEmployee->id->toBe($employee->id);

    expect($employee->fresh())->toBeNull();

    expect($shop->fresh())->not->toBeNull(); // disconnectするだけで、Shopは削除されてないことを確認
    expect($employeeGroup->fresh())->not->toBeNull(); // disconnectするだけで、EmployeeGroupは削除されてないことを確認
});
