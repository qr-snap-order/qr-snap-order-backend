<?php

namespace App\Models\Concerns\Employee;

use App\Models\Employee;
use App\Models\Shop;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin Employee
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, Employee>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class)->guardNull();
    }

    /**
     * @return BelongsToMany<Shop>
     */
    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class);
    }
}
