<?php

use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('employeeGroup query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $employeeGroup = EmployeeGroup::factory(['name' => '社員'])
        ->for($tenant)
        ->hasAttached(Employee::factory(['name' => '山田'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(Employee::factory(['name' => '田中'])->for($tenant), ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        query {
            employeeGroup(id: "{$employeeGroup->id}") {
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
        ->toHaveKey('data.employeeGroup')
        ->data->employeeGroup->name->toBe('社員')
        ->data->employeeGroup->employees->toHaveCount(2)
        ->data->employeeGroup->employees->{0}->name->toBe('山田')
        ->data->employeeGroup->employees->{1}->name->toBe('田中');
});
