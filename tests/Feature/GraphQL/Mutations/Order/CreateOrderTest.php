<?php

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\Order;
use App\Models\OrderItemStatus;
use App\Models\Shop;
use App\Models\ShopTable;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

test('createOrder mutation', function () {
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
        ->toHaveKey('data.createOrder')
        ->data->createOrder->id->toBeString()
        ->data->createOrder->orderItems->toHaveCount(1)
        ->data->createOrder->orderItems->{0}->menuItem->id->toBe($menuItem->id)
        ->data->createOrder->orderItems->{0}->orderItemStatus->id->toBe($orderItemStatus->id)
        ->data->createOrder->orderItems->{0}->orderItemHistories->toHaveCount(1) // ステータスを変更すると履歴が作成される
        ->data->createOrder->orderItems->{0}->orderItemHistories->{0}->orderItemStatus->id->toBe($orderItemStatus->id)
        ->data->createOrder->orderItems->{0}->orderItemHistories->{0}->userable->id->toBe($user->id)
        ->data->createOrder->orderItems->{0}->count->toBe(2)
        ->data->createOrder->orderItems->{0}->price->toBe($menuItem->price);

    expect(Order::findOrFail($response->json('data.createOrder.id')))
        ->id->toBeString()
        ->orderItems->toHaveCount(1)
        ->orderItems->get(0)->menuItem->id->toBe($menuItem->id)
        ->orderItems->get(0)->orderItemStatus->id->toBe($orderItemStatus->id)
        ->orderItems->get(0)->orderItemHistories->toHaveCount(1) // ステータスを変更すると履歴が作成される
        ->orderItems->get(0)->orderItemHistories->get(0)->orderItemStatus->id->toBe($orderItemStatus->id)
        ->orderItems->get(0)->orderItemHistories->get(0)->userable->id->toBe($user->id)
        ->orderItems->get(0)->count->toBe(2)
        ->orderItems->get(0)->price->toBe($menuItem->price);
});
