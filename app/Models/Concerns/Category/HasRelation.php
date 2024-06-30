<?php

namespace App\Models\Concerns\Category;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin Category
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, Category>
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
        return $this->belongsToMany(MenuItem::class, 'menu_item_category');
    }
}
