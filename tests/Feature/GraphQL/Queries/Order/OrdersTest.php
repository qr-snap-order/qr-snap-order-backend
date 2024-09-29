<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemStatus;
use App\Models\Shop;
use App\Models\ShopTable;
use App\Models\Tenant;
use App\Models\User;
use Carbon\CarbonImmutable;
use Tests\TestCase;

test('orders query', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $menu = Menu::factory()->for($tenant)->create();
    $menuSection = MenuSection::factory()->for($tenant)->for($menu)->create();
    $menuItem = MenuItem::factory()->for($tenant)->for($menuSection)->create();

    $shop = Shop::factory(['id' => '00000000-0000-0000-0000-000000000000'])->for($tenant)->create();
    $shopTable = ShopTable::factory()->for($tenant)->for($shop)->create();

    $orderItemStatus = OrderItemStatus::factory()->for($tenant)->create();

    $order = Order::factory(['created_at' => CarbonImmutable::parse('2024-01-05 00:00:00')])->for($tenant)->for($shopTable)->create();
    $orderItem = OrderItem::factory([
        'price' => $menuItem->price,
        'count' => 1,
    ])->for($tenant)->for($order)->for($menuItem)->for($orderItemStatus)->createQuietly();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(/** @lang GraphQL */ <<<GRAPHQL
        query {
            orders (
                shop_id: "00000000-0000-0000-0000-000000000000"
                date: "2024-01-05"
            ) {
                id
                shopTable {
                    id
                }
                created_at
                orderItems {
                    id
                    menuItem {
                        id
                    }
                    orderItemStatus {
                        id
                    }
                    count
                    price
                }
            }
        }
        GRAPHQL
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.orders')
        ->data->orders->toHaveCount(1)
        ->data->orders->{0}->shopTable->id->toBe($shopTable->id)
        ->data->orders->{0}->created_at->toBe('2024-01-05 00:00:00')
        ->data->orders->{0}->orderItems->toHaveCount(1)
        ->data->orders->{0}->orderItems->{0}->id->toBe($orderItem->id)
        ->data->orders->{0}->orderItems->{0}->menuItem->id->toBe($menuItem->id)
        ->data->orders->{0}->orderItems->{0}->orderItemStatus->id->toBe($orderItemStatus->id)
        ->data->orders->{0}->orderItems->{0}->count->toBe(1)
        ->data->orders->{0}->orderItems->{0}->price->toBe($menuItem->price);
});

test('orders query filter', function (array $stored, string $queryParams, bool $expected) {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $shop = Shop::factory($stored['shop'])->for($tenant)->create();
    $shopTable = ShopTable::factory()->for($tenant)->for($shop)->create();

    Order::factory($stored['order'])->for($tenant)->for($shopTable)->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(/** @lang GraphQL */ <<<GRAPHQL
        query {
            orders (
                {$queryParams}
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
        ->toHaveKey('data.orders')
        ->data->orders->toHaveCount($expected ? 1 : 0);
})->with([
    '指定店舗が一致している場合' => [
        'stored' => [
            'shop' => [
                'id' => '00000000-0000-0000-0000-000000000000',
            ],
            'order' => [
                'created_at' => CarbonImmutable::parse('2024-01-05 00:00:00'),
            ],
        ],
        'queryParams' => /** @lang GraphQL */ <<<GRAPHQL
            shop_id: "00000000-0000-0000-0000-000000000000"
            date: "2024-01-05"
        GRAPHQL,
        'expected' => true,
    ],
    '指定店舗が一致していない場合' => [
        'stored' => [
            'shop' => [
                'id' => '00000000-0000-0000-0000-000000000000',
            ],
            'order' => [
                'created_at' => CarbonImmutable::parse('2024-01-05 00:00:00'),
            ],
        ],
        'queryParams' => /** @lang GraphQL */ <<<GRAPHQL
            shop_id: "00000000-0000-0000-0000-000000000001"
            date: "2024-01-05"
        GRAPHQL,
        'expected' => false,
    ],
    '日付が一致している場合（下限）' => [
        'stored' => [
            'shop' => [
                'id' => '00000000-0000-0000-0000-000000000000',
            ],
            'order' => [
                'created_at' => CarbonImmutable::parse('2024-01-05 00:00:00'),
            ],
        ],
        'queryParams' => /** @lang GraphQL */ <<<GRAPHQL
            shop_id: "00000000-0000-0000-0000-000000000000"
            date: "2024-01-05"
        GRAPHQL,
        'expected' => true,
    ],
    '日付が一致していない場合（下限）' => [
        'stored' => [
            'shop' => [
                'id' => '00000000-0000-0000-0000-000000000000',
            ],
            'order' => [
                'created_at' => CarbonImmutable::parse('2024-01-04 23:59:59'),
            ],
        ],
        'queryParams' => /** @lang GraphQL */ <<<GRAPHQL
            shop_id: "00000000-0000-0000-0000-000000000000"
            date: "2024-01-05"
        GRAPHQL,
        'expected' => false,
    ],
    '日付が一致している場合（上限）' => [
        'stored' => [
            'shop' => [
                'id' => '00000000-0000-0000-0000-000000000000',
            ],
            'order' => [
                'created_at' => CarbonImmutable::parse('2024-01-05 23:59:59'),
            ],
        ],
        'queryParams' => /** @lang GraphQL */ <<<GRAPHQL
            shop_id: "00000000-0000-0000-0000-000000000000"
            date: "2024-01-05"
        GRAPHQL,
        'expected' => true,
    ],
    '日付が一致していない場合（上限）' => [
        'stored' => [
            'shop' => [
                'id' => '00000000-0000-0000-0000-000000000000',
            ],
            'order' => [
                'created_at' => CarbonImmutable::parse('2024-01-06 00:00:00'),
            ],
        ],
        'queryParams' => /** @lang GraphQL */ <<<GRAPHQL
            shop_id: "00000000-0000-0000-0000-000000000000"
            date: "2024-01-05"
        GRAPHQL,
        'expected' => false,
    ],
]);
