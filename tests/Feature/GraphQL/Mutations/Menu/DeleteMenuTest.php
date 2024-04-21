<?php

use App\Models\Menu;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('deleteMenu mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $deletingMenu = Menu::factory()->for($tenant)->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(
        /** @lang GraphQL */ <<<GRAPHQL
        mutation {
            deleteMenu(
                id: "{$deletingMenu->id}"
            ) {
                id
            }
        }
        GRAPHQL
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.deleteMenu')
        ->data->deleteMenu->id->toBe($deletingMenu->id);

    expect($deletingMenu->fresh())->toBeNull();
});
