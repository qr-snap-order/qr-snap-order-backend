<?php

use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('updateEmployeeGroup mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $employees = Employee::factory()
        ->forEachSequence(
            ['name' => '山田'],
            ['name' => '田中'],
            ['name' => '佐藤'],
        )
        ->for($tenant)
        ->create();

    $employeeGroup = EmployeeGroup::factory()
        ->for($tenant)
        ->hasAttached($employees[0], ['tenant_id' => $tenant->id])
        ->hasAttached($employees[1], ['tenant_id' => $tenant->id])
        ->create();


    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            updateEmployeeGroup (
                id: "{$employeeGroup->id}"
                name: "マネージャー"
                employees: ["{$employees[1]->id}", "{$employees[2]->id}"]

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
        ->toHaveKey('data.updateEmployeeGroup')
        ->data->updateEmployeeGroup->id->toBe($employeeGroup->id)
        ->data->updateEmployeeGroup->name->toBe('マネージャー')
        ->data->updateEmployeeGroup->employees->toHaveCount(2)
        ->data->updateEmployeeGroup->employees->{0}->name->toBe('田中')
        ->data->updateEmployeeGroup->employees->{1}->name->toBe('佐藤');

    expect($employeeGroup->fresh())
        ->name->toBe('マネージャー')
        ->employees->toHaveCount(2)
        ->employees->get(0)->name->toBe('田中')
        ->employees->get(1)->name->toBe('佐藤');

    expect($employees[0]->fresh())->not->toBeNull(); // disconnectするだけで、Employeeは削除されてないことを確認
});
