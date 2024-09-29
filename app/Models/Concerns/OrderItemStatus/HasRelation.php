<?php

namespace App\Models\Concerns\OrderItemStatus;

use App\Models\OrderItemStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin OrderItemStatus
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, OrderItemStatus>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
