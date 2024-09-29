<?php

namespace App\Models\Concerns\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShopTable;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Order
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, Order>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return BelongsTo<ShopTable, Order>
     */
    public function shopTable(): BelongsTo
    {
        return $this->belongsTo(ShopTable::class);
    }

    /**
     * @return HasMany<OrderItem>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
