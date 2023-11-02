<?php

namespace App\Models\Concerns\Staff;

use App\Models\Organization;
use App\Models\Shop;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin Staff
 */
trait HasRelation
{
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class);
    }
}
