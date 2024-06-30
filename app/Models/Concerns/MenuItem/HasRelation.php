<?php

namespace App\Models\Concerns\MenuItem;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin MenuItem
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, MenuItem>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class)->guardNull();
    }

    /**
     * @return BelongsTo<MenuSection, MenuItem>
     */
    public function menuSection(): BelongsTo
    {
        return $this->BelongsTo(MenuSection::class)->guardNull();
    }

    /**
     * @return BelongsToMany<Category>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'menu_item_category');
    }
}
