<?php

namespace App\Models;

use App\Models\Concerns\OrderItem\HasRelation;
use App\Models\OrderItemStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class OrderItem extends Model
{
    use CascadesDeletes;
    use HasFactory;
    use HasUuids;
    use HasRelation;

    /**
     * @var array<int, string> $cascadeDeletes
     */
    protected array $cascadeDeletes = ['orderItemHistories'];

    public static function boot()
    {
        parent::boot();

        static::creating(function (self $orderItem) {
            throw_unless(
                $orderItem->menuItem,
                \LogicException::class, // TODO:: 適切な例外に置き換える
            );

            throw_unless(
                $orderItem->price === $orderItem->menuItem->price,
                \Exception::class, // TODO:: 適切な例外に置き換える
                "{$orderItem->name}は商品価格が変更されました。ご確認の上、再度注文をお願いします。" // TODO:: 多言語化
            );
        });

        static::saving(function (self $orderItem) {
            // TODO:: 権限チェックが必要
        });

        static::creating(function (self $orderItem) {
            // TODO:: キャッシュで効率化（Tenantコンテキストに読み込むのかモデルクラスにキャッシュ機能を実装するのか）
            $firstOrderItemStatus = OrderItemStatus::whereSortKey(1)->firstOrFail();

            $orderItem->orderItemStatus()->associate($firstOrderItemStatus);
        });

        static::saved(function (self $orderItem) {
            if ($orderItem->isDirty('order_item_status_id')) {

                $orderItemHistory = new OrderItemHistory();
                $orderItemHistory->orderItem()->associate($orderItem);
                $orderItemHistory->userable()->associate(auth()->user()); // TODO:: 従業員、お客がはいることもある。
                $orderItemHistory->order_item_status_id = $orderItem->order_item_status_id;
                $orderItemHistory->save();
            }
        });
    }
}
