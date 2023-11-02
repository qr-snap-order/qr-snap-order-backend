<?php

namespace App\Models\Concerns\Shop;

use App\Models\Organization;
use App\Models\Shop;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin Shop
 */
trait HasRelation
{
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function staffs(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class);
    }
}
