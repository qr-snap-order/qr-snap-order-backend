<?php

namespace App\Models\Concerns\OrderItem;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemHistory;
use App\Models\OrderItemStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin OrderItem
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, OrderItem>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return BelongsTo<Order, OrderItem>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<MenuItem, OrderItem>
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    /**
     * @return BelongsTo<OrderItemStatus, OrderItem>
     */
    public function orderItemStatus(): BelongsTo
    {
        return $this->belongsTo(OrderItemStatus::class);
    }

    /**
     * @return HasMany<OrderItemHistory>
     */
    public function orderItemHistories(): HasMany
    {
        return $this->hasMany(OrderItemHistory::class);
    }
}
