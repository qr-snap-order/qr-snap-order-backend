<?php

use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('deleteEmployeeGroup mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $employee = Employee::factory()->for($tenant)->create();

    $employeeGroup = EmployeeGroup::factory()
        ->for($tenant)
        ->hasAttached($employee, ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            deleteEmployeeGroup (
                id: "{$employeeGroup->id}"

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
        ->toHaveKey('data.deleteEmployeeGroup')
        ->data->deleteEmployeeGroup->id->toBe($employeeGroup->id);

    expect($employeeGroup->fresh())->toBeNull();

    expect($employee->fresh())->not->toBeNull(); // disconnectするだけで、Employeeは削除されてないことを確認
});
