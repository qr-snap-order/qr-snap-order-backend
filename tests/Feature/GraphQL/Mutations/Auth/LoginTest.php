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

test('Authorizationヘッダーでアクセストークンを指定していない場合、認証が必要なクエリを実行すると認証エラーになること', function () {
    /** @var TestCase $this */

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
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->toHaveKey('errors.0.message', 'Unauthenticated.');
});

test('login mutationで有効なアクセストークンを取得できること', function () {
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

    $shop = Shop::factory()->for($tenant)->create();

    // トークンの有効・無効をチェックしたいので、内部に認証ユーザーが保持されていないことを確認しておく
    $tokenGuard = auth()->guard('api');
    assert(!$tokenGuard->user());

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
        ->not->toHaveKey('errors')
        ->toHaveKey('data.shop.id', $shop->id);
});
