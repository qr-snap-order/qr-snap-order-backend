<?php

namespace App\Models\Concerns\MenuItemGroup;

use App\Models\MenuItem;
use App\Models\MenuItemGroup;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin MenuItemGroup
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, MenuItemGroup>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class)->guardNull();
    }

    /**
     * @return BelongsToMany<MenuItem>
     */
    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_group_assignments');
    }
}
