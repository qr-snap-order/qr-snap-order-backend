<?php

use App\Models\Employee;
use App\Models\Shop;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('shops query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    Shop::factory(['name' => '東京支店'])
        ->for($tenant)
        ->hasAttached(Employee::factory(['name' => '田中'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(Employee::factory(['name' => '佐藤'])->for($tenant), ['tenant_id' => $tenant->id])
        ->create();

    Shop::factory(['name' => '千葉支店'])
        ->for($tenant)
        ->hasAttached(Employee::factory(['name' => '鈴木'])->for($tenant), ['tenant_id' => $tenant->id])
        ->hasAttached(Employee::factory(['name' => '加藤'])->for($tenant), ['tenant_id' => $tenant->id])
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        query {
            shops {
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
        ->toHaveKey('data.shops')
        ->data->shops->{0}->name->toBe('東京支店')
        ->data->shops->{0}->employees->toHaveCount(2)
        ->data->shops->{0}->employees->{0}->name->toBe('田中')
        ->data->shops->{0}->employees->{1}->name->toBe('佐藤')
        ->data->shops->{1}->name->toBe('千葉支店')
        ->data->shops->{1}->employees->toHaveCount(2)
        ->data->shops->{1}->employees->{0}->name->toBe('鈴木')
        ->data->shops->{1}->employees->{1}->name->toBe('加藤');
});
