<?php

use App\Models\Tenant;
use Tests\TestCase;

test('hello query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $response = $this->graphQL(
        /** @lang GraphQL */
        'query {
            hello
        }'
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.hello')
        ->data->hello->toBe('hello');
});
