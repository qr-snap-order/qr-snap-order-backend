<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemHistory;
use App\Models\OrderItemStatus;
use App\Models\Shop;
use App\Models\ShopTable;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('updateOrder mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $menu = Menu::factory()->for($tenant)->create();
    $menuSection = MenuSection::factory()->for($tenant)->for($menu)->create();
    $menuItems = MenuItem::factory()->for($tenant)->for($menuSection)->count(3)->create();

    $shop = Shop::factory()->for($tenant)->create();
    $shopTables = ShopTable::factory()->for($tenant)->for($shop)->count(2)->create();

    $orderItemStatuses = OrderItemStatus::factory()->for($tenant)->forEachSequence(
        ['sort_key' => 1],
        ['sort_key' => 2],
    )->create();

    $order = Order::factory()->for($tenant)->for($shopTables[0])->create();

    $orderItem = OrderItem::factory()
        ->for($tenant)
        ->for($order)
        ->for($menuItems[0])
        ->for($orderItemStatuses[0])
        ->has(OrderItemHistory::factory()->for($tenant)->for($orderItemStatuses[0])->for($user, 'userable'))
        ->createQuietly();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(/** @lang GraphQL */ <<<GRAPHQL
            mutation {
                updateOrder(
                    id: "{$order->id}"
                    shop_table_id: "{$shopTables[1]->id}"
                    orderItems: [
                        {
                            id: "{$orderItem->id}"
                            menu_item_id: "{$menuItems[1]->id}"
                            count: 2
                            price: 1000
                            order_item_status_id: "{$orderItemStatuses[1]->id}"
                        }
                        {
                            menu_item_id: "{$menuItems[2]->id}"
                            count: 2
                            price: {$menuItems[2]->price}
                        }
                    ]
                ) {
                    id
                    shopTable {
                        id
                    }
                    orderItems {
                        menuItem {
                            id
                        }
                        orderItemStatus {
                            id
                        }
                        orderItemHistories {
                            id
                            orderItemStatus {
                                id
                            }
                            userable {
                                id
                            }
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
        ->toHaveKey('data.updateOrder')
        ->data->updateOrder->id->toBeString()
        ->data->updateOrder->shopTable->id->toBe($shopTables[1]->id)
        ->data->updateOrder->orderItems->toHaveCount(2)
        ->data->updateOrder->orderItems->{0}->menuItem->id->toBe($menuItems[1]->id)
        ->data->updateOrder->orderItems->{0}->orderItemStatus->id->toBe($orderItemStatuses[1]->id)
        ->data->updateOrder->orderItems->{0}->orderItemHistories->toHaveCount(2) // ステータスを変更すると履歴が作成される
        ->data->updateOrder->orderItems->{0}->orderItemHistories->{0}->orderItemStatus->id->toBe($orderItemStatuses[0]->id)
        ->data->updateOrder->orderItems->{0}->orderItemHistories->{0}->userable->id->toBe($user->id)
        ->data->updateOrder->orderItems->{0}->orderItemHistories->{1}->orderItemStatus->id->toBe($orderItemStatuses[1]->id)
        ->data->updateOrder->orderItems->{0}->orderItemHistories->{1}->userable->id->toBe($user->id)
        ->data->updateOrder->orderItems->{0}->count->toBe(2)
        ->data->updateOrder->orderItems->{0}->price->toBe(1000)
        ->data->updateOrder->orderItems->{1}->menuItem->id->toBe($menuItems[2]->id)
        ->data->updateOrder->orderItems->{1}->orderItemStatus->id->toBe($orderItemStatuses[0]->id)
        ->data->updateOrder->orderItems->{1}->orderItemHistories->toHaveCount(1) // ステータスを変更すると履歴が作成される
        ->data->updateOrder->orderItems->{1}->orderItemHistories->{0}->orderItemStatus->id->toBe($orderItemStatuses[0]->id)
        ->data->updateOrder->orderItems->{1}->orderItemHistories->{0}->userable->id->toBe($user->id)
        ->data->updateOrder->orderItems->{1}->count->toBe(2)
        ->data->updateOrder->orderItems->{1}->price->toBe($menuItems[2]->price);

    expect($order->fresh([
        'orderItems.menuItem',
        'orderItems.orderItemStatus',
        'orderItems.orderItemHistories.orderItemStatus',
        'orderItems.orderItemHistories.userable',
    ]))
        ->id->toBeString()
        ->orderItems->toHaveCount(2)
        ->orderItems->get(0)->menuItem->id->toBe($menuItems[1]->id)
        ->orderItems->get(0)->orderItemStatus->id->toBe($orderItemStatuses[1]->id)
        ->orderItems->get(0)->orderItemHistories->toHaveCount(2) // ステータスを変更すると履歴が作成される
        ->orderItems->get(0)->orderItemHistories->get(0)->orderItemStatus->id->toBe($orderItemStatuses[0]->id)
        ->orderItems->get(0)->orderItemHistories->get(0)->userable->id->toBe($user->id)
        ->orderItems->get(0)->orderItemHistories->get(1)->orderItemStatus->id->toBe($orderItemStatuses[1]->id)
        ->orderItems->get(0)->orderItemHistories->get(1)->userable->id->toBe($user->id)
        ->orderItems->get(0)->count->toBe(2)
        ->orderItems->get(0)->price->toBe(1000)
        ->orderItems->get(1)->menuItem->id->toBe($menuItems[2]->id)
        ->orderItems->get(1)->orderItemStatus->id->toBe($orderItemStatuses[0]->id)
        ->orderItems->get(1)->orderItemHistories->toHaveCount(1) // ステータスを変更すると履歴が作成される
        ->orderItems->get(1)->orderItemHistories->get(0)->orderItemStatus->id->toBe($orderItemStatuses[0]->id)
        ->orderItems->get(1)->orderItemHistories->get(0)->userable->id->toBe($user->id)
        ->orderItems->get(1)->count->toBe(2)
        ->orderItems->get(1)->price->toBe($menuItems[2]->price);
});
