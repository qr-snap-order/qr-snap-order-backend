<?php

use App\Models\OrderItemStatus;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('orderItemStatuses query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    OrderItemStatus::factory()->for($tenant)
        ->forEachSequence(
            [
                'name' => '受付',
                'color' => '#FFFFFF',
                'sort_key' => 1,
            ],
            [
                'name' => '調理中',
                'color' => '#FFFFFF',
                'sort_key' => 2,
            ],
            [
                'name' => '調理済',
                'color' => '#FFFFFF',
                'sort_key' => 3,
            ],
            [
                'name' => '配膳中',
                'color' => '#FFFFFF',
                'sort_key' => 4,
            ],
            [
                'name' => '配膳済',
                'color' => '#FFFFFF',
                'sort_key' => 5,
            ],
        )
        ->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(/** @lang GraphQL */ <<<GRAPHQL
        query {
            orderItemStatuses {
                id
                name
                color
            }
        }
        GRAPHQL
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.orderItemStatuses')
        ->data->orderItemStatuses->toHaveCount(5)
        ->data->orderItemStatuses->{0}->name->toBe('受付')
        ->data->orderItemStatuses->{0}->color->toBe('#FFFFFF')
        ->data->orderItemStatuses->{1}->name->toBe('調理中')
        ->data->orderItemStatuses->{1}->color->toBe('#FFFFFF')
        ->data->orderItemStatuses->{2}->name->toBe('調理済')
        ->data->orderItemStatuses->{2}->color->toBe('#FFFFFF')
        ->data->orderItemStatuses->{3}->name->toBe('配膳中')
        ->data->orderItemStatuses->{3}->color->toBe('#FFFFFF')
        ->data->orderItemStatuses->{4}->name->toBe('配膳済')
        ->data->orderItemStatuses->{4}->color->toBe('#FFFFFF');
});
