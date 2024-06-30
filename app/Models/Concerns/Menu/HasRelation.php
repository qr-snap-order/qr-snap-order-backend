<?php

namespace App\Models\Concerns\Menu;

use App\Models\Menu;
use App\Models\MenuSection;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Menu
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, Menu>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class)->guardNull();
    }

    /**
     * @return HasMany<MenuSection>
     */
    public function menuSections(): HasMany
    {
        return $this->hasMany(MenuSection::class);
    }
}
