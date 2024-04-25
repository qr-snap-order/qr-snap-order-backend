<?php

namespace App\Models\Concerns\ShopGroup;

use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\ShopGroupShop;
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
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return BelongsToMany<Shop>
     */
    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class, 'shop_group_shop')->using(ShopGroupShop::class);
    }
}
