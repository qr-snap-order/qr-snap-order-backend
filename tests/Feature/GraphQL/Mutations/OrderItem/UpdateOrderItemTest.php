<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\OrderItemStatus;
use App\Models\Shop;
use App\Models\ShopTable;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('updateOrderItem mutation', function () {
    /** @var TestCase $this */

    $tenant = Tenant::factory()->create();

    $user = User::factory()->for($tenant)->create();

    $menu = Menu::factory()->for($tenant)->create();
    $menuSection = MenuSection::factory()->for($tenant)->for($menu)->create();
    $menuItem = MenuItem::factory()->for($tenant)->for($menuSection)->create();

    $shop = Shop::factory(['id' => '00000000-0000-0000-0000-000000000000'])->for($tenant)->create();
    $shopTable = ShopTable::factory()->for($tenant)->for($shop)->create();

    $orderItemStatus = OrderItemStatus::factory(['sort_key' => 1])->for($tenant)->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(/** @lang GraphQL */ <<<GRAPHQL
            mutation {
                createOrder(
                    shop_table_id: "{$shopTable->id}"
                    orderItems: [
                        {
                            menu_item_id: "{$menuItem->id}"
                            count: 2
                            price: {$menuItem->price}
                        }
                    ]
                ) {
                    id
                    orderItems {
                        id
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
                    }
                }
            }
        GRAPHQL
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.createOrder')
        ->data->createOrder->id->toBeString()
        ->data->createOrder->orderItems->toHaveCount(1)
        ->data->createOrder->orderItems->{0}->orderItemStatus->id->toBe($orderItemStatus->id)
        ->data->createOrder->orderItems->{0}->orderItemHistories->toHaveCount(1) // ステータスを変更すると履歴が作成される
        ->data->createOrder->orderItems->{0}->orderItemHistories->{0}->orderItemStatus->id->toBe($orderItemStatus->id)
        ->data->createOrder->orderItems->{0}->orderItemHistories->{0}->userable->id->toBe($user->id);

    $orderItemId = $response->json('data.createOrder.orderItems.0.id');
    $updatingOrderItemStatus = OrderItemStatus::factory(['sort_key' => 2])->for($tenant)->create();

    $response = $this->domain($tenant)->actingAs($user)->graphQL(/** @lang GraphQL */ <<<GRAPHQL
            mutation {
                updateOrderItem(
                    id: "{$orderItemId}"
                    order_item_status_id: "{$updatingOrderItemStatus->id}"
                ) {
                    id
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
                }
            }
        GRAPHQL
    );

    expect($response->content())
        ->toBeJson()
        ->json()
        ->not->toHaveKey('errors')
        ->toHaveKey('data.updateOrderItem')
        ->data->updateOrderItem->id->toBeString()
        ->data->updateOrderItem->orderItemStatus->id->toBe($updatingOrderItemStatus->id)
        ->data->updateOrderItem->orderItemHistories->toHaveCount(2) // ステータスを変更すると履歴が作成される
        ->data->updateOrderItem->orderItemHistories->{0}->orderItemStatus->id->toBe($orderItemStatus->id)
        ->data->updateOrderItem->orderItemHistories->{0}->userable->id->toBe($user->id)
        ->data->updateOrderItem->orderItemHistories->{1}->orderItemStatus->id->toBe($updatingOrderItemStatus->id)
        ->data->updateOrderItem->orderItemHistories->{1}->userable->id->toBe($user->id);
});
