<?php

use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('createEmployeeGroup mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $employees = Employee::factory()
        ->forEachSequence(
            ['name' => '山田'],
            ['name' => '田中'],
        )
        ->for($tenant)
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            createEmployeeGroup (
                name: "マネージャー"
                employees: ["{$employees[0]->id}", "{$employees[1]->id}"]

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
        ->toHaveKey('data.createEmployeeGroup')
        ->data->createEmployeeGroup->name->toBe('マネージャー')
        ->data->createEmployeeGroup->employees->toHaveCount(2)
        ->data->createEmployeeGroup->employees->{0}->name->toBe('山田')
        ->data->createEmployeeGroup->employees->{1}->name->toBe('田中');

    expect(EmployeeGroup::findOrFail($response->json('data.createEmployeeGroup.id')))
        ->name->toBe('マネージャー')
        ->employees->toHaveCount(2)
        ->employees->get(0)->name->toBe('山田')
        ->employees->get(1)->name->toBe('田中');
});
