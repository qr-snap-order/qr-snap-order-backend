<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemStatus;
use App\Models\ShopTable;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::findOrFail('00000000-0000-0000-0000-000000000000');

        $shopTables = ShopTable::all();

        $menuItems = MenuItem::all();

        $orderItemStatuses = OrderItemStatus::all();

        Order::factory()
            ->for($tenant)
            ->for($shopTables[0])
            ->has(
                OrderItem::factory([
                    'count' => 1,
                    'price' => $menuItems[0]->price,
                ])
                    ->for($tenant)
                    ->for($menuItems[0])
                    ->for($orderItemStatuses[0])
            )
            ->has(
                OrderItem::factory([
                    'count' => 1,
                    'price' => $menuItems[1]->price,
                ])
                    ->for($tenant)
                    ->for($menuItems[1])
                    ->for($orderItemStatuses[0])
            )
            ->createQuietly();

        Order::factory()
            ->for($tenant)
            ->for($shopTables[1])
            ->has(
                OrderItem::factory([
                    'count' => 1,
                    'price' => $menuItems[2]->price,
                ])
                    ->for($tenant)
                    ->for($menuItems[2])
                    ->for($orderItemStatuses[0])
            )
            ->has(
                OrderItem::factory([
                    'count' => 1,
                    'price' => $menuItems[3]->price,
                ])
                    ->for($tenant)
                    ->for($menuItems[3])
                    ->for($orderItemStatuses[0])
            )
            ->createQuietly();

        Order::factory()
            ->for($tenant)
            ->for($shopTables[2])
            ->has(
                OrderItem::factory([
                    'count' => 1,
                    'price' => $menuItems[4]->price,
                ])
                    ->for($tenant)
                    ->for($menuItems[4])
                    ->for($orderItemStatuses[0])
            )
            ->has(
                OrderItem::factory([
                    'count' => 1,
                    'price' => $menuItems[5]->price,
                ])
                    ->for($tenant)
                    ->for($menuItems[5])
                    ->for($orderItemStatuses[0])
            )
            ->createQuietly();
    }
}
