<?php

namespace App\Models\Concerns\ShopGroup;

use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\ShopGroupAssignment;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin ShopGroup
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, ShopGroup>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class)->guardNull();
    }

    /**
     * @return BelongsToMany<Shop>
     */
    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class, 'shop_group_assignments')->using(ShopGroupAssignment::class);
    }
}
