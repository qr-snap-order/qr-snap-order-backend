<?php

namespace App\Models\Concerns\OrderItemHistory;

use App\Models\OrderItem;
use App\Models\OrderItemHistory;
use App\Models\OrderItemStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin OrderItemHistory
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, OrderItemHistory>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return BelongsTo<OrderItem, OrderItemHistory>
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * @return BelongsTo<OrderItemStatus, OrderItemHistory>
     */
    public function orderItemStatus(): BelongsTo
    {
        return $this->belongsTo(OrderItemStatus::class);
    }

    /**
     * @return MorphTo<Model, OrderItemHistory>
     */
    public function userable(): MorphTo
    {
        return $this->morphTo();
    }
}
