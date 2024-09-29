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

test('Order query', function () {
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
            order (
                id: "{$order->id}"
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
        ->toHaveKey('data.order')
        ->data->order->shopTable->id->toBe($shopTable->id)
        ->data->order->created_at->toBe('2024-01-05 00:00:00')
        ->data->order->orderItems->toHaveCount(1)
        ->data->order->orderItems->{0}->id->toBe($orderItem->id)
        ->data->order->orderItems->{0}->menuItem->id->toBe($menuItem->id)
        ->data->order->orderItems->{0}->orderItemStatus->id->toBe($orderItemStatus->id)
        ->data->order->orderItems->{0}->count->toBe(1)
        ->data->order->orderItems->{0}->price->toBe($menuItem->price);
});
