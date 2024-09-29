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
use Tests\TestCase;

test('deleteOrder mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $menu = Menu::factory()->for($tenant)->create();
    $menuSection = MenuSection::factory()->for($tenant)->for($menu)->create();
    $menuItem = MenuItem::factory()->for($tenant)->for($menuSection)->create();

    $shop = Shop::factory()->for($tenant)->create();
    $shopTable = ShopTable::factory()->for($tenant)->for($shop)->create();

    $orderItemStatus = OrderItemStatus::factory()->for($tenant)->create();

    $order = Order::factory()->for($tenant)->for($shopTable)->create();

    $orderItem = OrderItem::factory()
        ->for($tenant)
        ->for($order)
        ->for($menuItem)
        ->for($orderItemStatus)
        ->createQuietly();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(/** @lang GraphQL */ <<<GRAPHQL
            mutation {
                deleteOrder(
                    id: "{$order->id}"
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
        ->toHaveKey('data.deleteOrder')
        ->data->deleteOrder->id->toBeString();

    expect($order->fresh())->toBeNull();
    expect($orderItem->fresh())->toBeNull();
});
