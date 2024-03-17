<?php

use App\Models\Shop;
use App\Models\Tenant;
use App\Models\User;
use Laravel\Passport\Client;
use Tests\TestCase;

beforeEach(function () {
    /** @var TestCase $this */

    /**
     * OAuthクライアントを作成する
     * - ID: 1 => Laravel Personal Access Client
     * - ID: 2 => Laravel Password Grant Client
     */
    $this->artisan('passport:install')->assertExitCode(0);

    /**
     * LighthouseGraphQLPassportの設定
     */
    $passwordClient = Client::wherePasswordClient(true)->firstOrFail();
    config([
        'lighthouse-graphql-passport.client_id' => $passwordClient->id,
        'lighthouse-graphql-passport.client_secret' => $passwordClient->secret,
    ]);
});

test('refreshToken mutationで有効な新しいアクセストークンを取得できること', function () {
    /** @var TestCase $this */

    $email = 'hoge@example.com';
    $password = '1234';

    User::factory()->create([
        'email' => $email,
        'password' => bcrypt($password),
    ]);

    $response = $this->graphQL(
        /** @lang GraphQL */
        'mutation ($email: String!, $password: String!) {
            login(
                input: {
                    username: $email,
                    password: $password
                }
            ) {
                refresh_token
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
        ->toHaveKey('data.login.refresh_token');

    $refreshToken = $response->json('data.login.refresh_token');

    $response = $this->graphQL(
        /** @lang GraphQL */
        'mutation ($refreshToken: String!) {
            refreshToken(
                input: {
                    refresh_token: $refreshToken,
                }
            ) {
                access_token
            }
        }',
        [
            'refreshToken' => $refreshToken,
        ]
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.refreshToken.access_token');

    $accessToken = $response->json('data.refreshToken.access_token');

    $tenant = Tenant::factory()->create();
    $shop = Shop::factory()->for($tenant)->create();

    // トークンの有効・無効をチェックしたいので、内部に認証ユーザーが保持されていないことを確認しておく
    $tokenGuard = auth()->guard('api');
    assert(!$tokenGuard->user());

    $response = $this->graphQL(
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
        ->not->toHaveKey('errors')
        ->toHaveKey('data.shop.id', $shop->id);
});

test('refreshToken mutationを実行すると、古いアクセストークンを無効になること', function () {
    /** @var TestCase $this */

    $email = 'hoge@example.com';
    $password = '1234';

    User::factory()->create([
        'email' => $email,
        'password' => bcrypt($password),
    ]);

    $response = $this->graphQL(
        /** @lang GraphQL */
        'mutation ($email: String!, $password: String!) {
            login(
                input: {
                    username: $email,
                    password: $password
                }
            ) {
                access_token
                refresh_token
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
        ->toHaveKey('data.login.refresh_token');

    $accessToken = $response->json('data.login.access_token');
    $refreshToken = $response->json('data.login.refresh_token');

    $response = $this->graphQL(
        /** @lang GraphQL */
        'mutation ($refreshToken: String!) {
            refreshToken(
                input: {
                    refresh_token: $refreshToken,
                }
            ) {
                access_token
            }
        }',
        [
            'refreshToken' => $refreshToken,
        ]
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors');

    $tenant = Tenant::factory()->create();
    $shop = Shop::factory()->for($tenant)->create();

    // トークンの有効・無効をチェックしたいので、内部に認証ユーザーが保持されていないことを確認しておく
    $tokenGuard = auth()->guard('api');
    assert(!$tokenGuard->user());

    $response = $this->graphQL(
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
