<?php

namespace App\Models\Concerns\Tenant;

use App\Models\Employee;
use App\Models\Shop;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Tenant
 */
trait HasRelation
{
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }
}
