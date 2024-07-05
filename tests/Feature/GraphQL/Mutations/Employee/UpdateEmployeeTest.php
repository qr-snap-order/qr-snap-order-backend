<?php

use App\Models\Employee;
use App\Models\EmployeeGroup;
use App\Models\Shop;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('updateEmployee mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $shops = Shop::factory()->forEachSequence(
        ['name' => '東京支店'],
        ['name' => '大阪支店'],
        ['name' => '福岡支店'],
    )->for($tenant)->create();

    $employeeGroups = EmployeeGroup::factory()
        ->forEachSequence(
            ['name' => 'マネージャー'],
            ['name' => '社員'],
            ['name' => 'アルバイト'],
        )
        ->for($tenant)
        ->create();

    $employee = Employee::factory()
        ->for($tenant)
        ->hasAttached($shops[0], ['tenant_id' => $tenant->id])
        ->hasAttached($shops[1], ['tenant_id' => $tenant->id])
        ->hasAttached($employeeGroups[0], ['tenant_id' => $tenant->id])
        ->hasAttached($employeeGroups[1], ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            updateEmployee (
                id: "{$employee->id}"
                name: "山田"
                shops: ["{$shops[1]->id}", "{$shops[2]->id}"]
                employeeGroups: ["{$employeeGroups[1]->id}", "{$employeeGroups[2]->id}"]
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
        ->toHaveKey('data.updateEmployee')
        ->data->updateEmployee->id->toBe($employee->id)
        ->data->updateEmployee->name->toBe('山田')
        ->data->updateEmployee->shops->toHaveCount(2)
        ->data->updateEmployee->shops->{0}->name->toBe('大阪支店')
        ->data->updateEmployee->shops->{1}->name->toBe('福岡支店')
        ->data->updateEmployee->employeeGroups->{0}->name->toBe('社員')
        ->data->updateEmployee->employeeGroups->{1}->name->toBe('アルバイト');

    expect($employee->fresh())
        ->name->toBe('山田')
        ->shops->toHaveCount(2)
        ->shops->get(0)->name->toBe('大阪支店')
        ->shops->get(1)->name->toBe('福岡支店')
        ->employeeGroups->get(0)->name->toBe('社員')
        ->employeeGroups->get(1)->name->toBe('アルバイト');

    expect($shops[0]->fresh())->not->toBeNull(); // disconnectするだけで、Shopは削除されてないことを確認
    expect($employeeGroups[0]->fresh())->not->toBeNull(); // disconnectするだけで、EmployeeGroupeは削除されてないことを確認
});
