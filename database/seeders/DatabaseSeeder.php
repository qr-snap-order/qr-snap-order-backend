<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\MenuItemGroupSeeder;
use Database\Seeders\ShopSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TenantSeeder::class,
            UserSeeder::class,
            ShopSeeder::class,
            EmployeeSeeder::class,
            MenuSeeder::class,
            OrderItemStatusSeeder::class, // TODO:: Tenant作成時に初期データとしてリスナーで実行する
            OrderSeeder::class,
        ]);
    }
}
