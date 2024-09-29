<?php

namespace App\Models\Concerns\ShopTable;

use App\Models\Shop;
use App\Models\ShopTable;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin ShopTable
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, ShopTable>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return BelongsTo<Shop, ShopTable>
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
