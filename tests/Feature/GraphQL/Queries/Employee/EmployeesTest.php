<?php

use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\Shop;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('employees query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    Employee::factory(['name' => '山田'])
        ->for($tenant)
        ->hasAttached(Shop::factory(['name' => '大阪支店'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(Shop::factory(['name' => '福岡支店'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(EmployeeGroup::factory(['name' => 'マネージャー'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(EmployeeGroup::factory(['name' => '社員'])->for($tenant), ['tenant_id' => $tenant->id])
        ->create();

    Employee::factory(['name' => '田中'])
        ->for($tenant)
        ->hasAttached(Shop::factory(['name' => '東京支店'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(Shop::factory(['name' => '千葉支店'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(EmployeeGroup::factory(['name' => 'アルバイト'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(EmployeeGroup::factory(['name' => '派遣'])->for($tenant), ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        query {
            employees {
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
        ->toHaveKey('data.employees')
        ->data->employees->{0}->name->toBe('山田')
        ->data->employees->{0}->shops->toHaveCount(2)
        ->data->employees->{0}->shops->{0}->name->toBe('大阪支店')
        ->data->employees->{0}->shops->{1}->name->toBe('福岡支店')
        ->data->employees->{0}->employeeGroups->toHaveCount(2)
        ->data->employees->{0}->employeeGroups->{0}->name->toBe('マネージャー')
        ->data->employees->{0}->employeeGroups->{1}->name->toBe('社員')
        ->data->employees->{1}->name->toBe('田中')
        ->data->employees->{1}->shops->toHaveCount(2)
        ->data->employees->{1}->shops->{0}->name->toBe('東京支店')
        ->data->employees->{1}->shops->{1}->name->toBe('千葉支店')
        ->data->employees->{1}->employeeGroups->toHaveCount(2)
        ->data->employees->{1}->employeeGroups->{0}->name->toBe('アルバイト')
        ->data->employees->{1}->employeeGroups->{1}->name->toBe('派遣');
});
