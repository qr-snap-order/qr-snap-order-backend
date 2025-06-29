<?php

use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('employeeGroups query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    EmployeeGroup::factory(['name' => 'マネージャー'])
        ->for($tenant)
        ->hasAttached(Employee::factory(['name' => '山田'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(Employee::factory(['name' => '田中'])->for($tenant), ['tenant_id' => $tenant->id])
        ->create();

    EmployeeGroup::factory(['name' => '社員'])
        ->for($tenant)
        ->hasAttached(Employee::factory(['name' => '佐藤'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(Employee::factory(['name' => '鈴木'])->for($tenant), ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        query {
            employeeGroups {
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
        ->toHaveKey('data.employeeGroups')
        ->data->employeeGroups->{0}->name->toBe('マネージャー')
        ->data->employeeGroups->{0}->employees->toHaveCount(2)
        ->data->employeeGroups->{0}->employees->{0}->name->toBe('山田')
        ->data->employeeGroups->{0}->employees->{1}->name->toBe('田中')
        ->data->employeeGroups->{1}->name->toBe('社員')
        ->data->employeeGroups->{1}->employees->toHaveCount(2)
        ->data->employeeGroups->{1}->employees->{0}->name->toBe('佐藤')
        ->data->employeeGroups->{1}->employees->{1}->name->toBe('鈴木');
});
