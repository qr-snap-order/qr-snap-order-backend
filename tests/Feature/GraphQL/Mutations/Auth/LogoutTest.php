<?php

use App\Models\Shop;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Passport\Client;
use Laravel\Passport\Guards\TokenGuard;
use Tests\TestCase;

beforeEach(function () {
    /** @var TestCase $this */

    /**
     * OAuthクライアントを作成する
     * - ID: 1 => Laravel Personal Access Client
     * - ID: 2 => Laravel Password Grant Client
     */
    $this->withoutMockingConsoleOutput()->artisan('passport:install', ['--no-interaction' => true]);

    /**
     * LighthouseGraphQLPassportの設定
     */
    $passwordClient = Client::wherePasswordClient(true)->firstOrFail();
    config([
        'lighthouse-graphql-passport.client_id' => $passwordClient->id,
        'lighthouse-graphql-passport.client_secret' => $passwordClient->secret,
    ]);
});

test('logout mutationを実行すると、Authorizationヘッダーで指定したアクセストークンが無効になること', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $email = 'hoge@example.com';
    $password = '1234';

    User::factory()->for($tenant)->create([
        'email' => $email,
        'password' => bcrypt($password),
    ]);

    $response = $this->domain($tenant)->graphQL(
        /** @lang GraphQL */
        'mutation ($email: String!, $password: String!) {
            login(
                input: {
                    username: $email,
                    password: $password
                }
            ) {
                access_token
            }
        }',
        [
            'email' => $email,
            'password' => $password,
        ]
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.login.access_token');

    $accessToken = $response->json('data.login.access_token');

    $response = $this->domain($tenant)->graphQL(
        /** @lang GraphQL */
        'mutation {
            logout {
                status
                message
            }
        }',
        headers: [
            'Authorization' => "Bearer {$accessToken}"
        ]
    );

    // 内部に保持される認証ユーザーを消さないと、トークンの有効・無効を確認するテストにならない。
    $tokenGuard = auth()->guard('api');
    assert($tokenGuard instanceof TokenGuard);
    $tokenGuard->forgetUser();

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.logout');

    $tenant = Tenant::factory()->create();
    $shop = Shop::factory()->for($tenant)->create();

    $response = $this->domain($tenant)->graphQL(
        /** @lang GraphQL */
        'query ($id: ID!) {
            shop(id: $id) {
                id
            }
        }',
        [
            'id' => $shop->id,
        ],
        headers: [
            'Authorization' => "Bearer {$accessToken}"
        ]
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->toHaveKey('errors.0.message', 'Unauthenticated.');
});
