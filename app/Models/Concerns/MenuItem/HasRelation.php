<?php

namespace App\Models\Concerns\MenuItem;

use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return BelongsTo<MenuSection, MenuItem>
     */
    public function menuSection(): BelongsTo
    {
        return $this->BelongsTo(MenuSection::class);
    }
}
