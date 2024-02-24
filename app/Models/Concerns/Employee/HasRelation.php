<?php

namespace App\Models\Concerns\Employee;

use App\Models\Employee;
use App\Models\Organization;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin Employee
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
