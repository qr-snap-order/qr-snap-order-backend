<?php

namespace App\Models\Concerns\MenuSection;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin MenuSection
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, MenuSection>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return BelongsTo<Menu, MenuSection>
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * @return HasMany<MenuItem>
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }
}
