<?php

use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\Shop;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('createEmployee mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $shops = Shop::factory()
        ->forEachSequence(
            ['name' => '東京支店'],
            ['name' => '大阪支店'],
        )
        ->for($tenant)
        ->create();

    $employeeGroups = EmployeeGroup::factory()
        ->forEachSequence(
            ['name' => 'マネージャー'],
            ['name' => '社員'],
        )
        ->for($tenant)
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(/** @lang GraphQL */ <<<GRAPHQL
        mutation {
            createEmployee (
                name: "山田"
                shops: ["{$shops[0]->id}", "{$shops[1]->id}"]
                employeeGroups: ["{$employeeGroups[0]->id}", "{$employeeGroups[1]->id}"]
            ) {
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
        ->toHaveKey('data.createEmployee')
        ->data->createEmployee->name->toBe('山田')
        ->data->createEmployee->shops->toHaveCount(2)
        ->data->createEmployee->shops->{0}->name->toBe('東京支店')
        ->data->createEmployee->shops->{1}->name->toBe('大阪支店')
        ->data->createEmployee->employeeGroups->toHaveCount(2)
        ->data->createEmployee->employeeGroups->{0}->name->toBe('マネージャー')
        ->data->createEmployee->employeeGroups->{1}->name->toBe('社員');

    expect(Employee::findOrFail($response->json('data.createEmployee.id')))
        ->name->toBe('山田')
        ->shops->toHaveCount(2)
        ->shops->get(0)->name->toBe('東京支店')
        ->shops->get(1)->name->toBe('大阪支店')
        ->employeeGroups->get(0)->name->toBe('マネージャー')
        ->employeeGroups->get(1)->name->toBe('社員');
});
