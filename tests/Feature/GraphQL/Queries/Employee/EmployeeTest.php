<?php

use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\Shop;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('employee query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $employee = Employee::factory(['name' => '山田'])
        ->hasAttached(Shop::factory(['name' => '東京支店'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(Shop::factory(['name' => '大阪支店'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(EmployeeGroup::factory(['name' => 'マネージャー'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(EmployeeGroup::factory(['name' => '社員'])->for($tenant), ['tenant_id' => $tenant->id])
        ->for($tenant)->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        query {
            employee(id: "{$employee->id}") {
                id
                name
                shops {
                    id
                    name
                }
                employeeGroups {
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
        ->toHaveKey('data.employee')
        ->data->employee->name->toBe('山田')
        ->data->employee->shops->toHaveCount(2)
        ->data->employee->shops->{0}->name->toBe('東京支店')
        ->data->employee->shops->{1}->name->toBe('大阪支店')
        ->data->employee->employeeGroups->{0}->name->toBe('マネージャー')
        ->data->employee->employeeGroups->{1}->name->toBe('社員');
});
