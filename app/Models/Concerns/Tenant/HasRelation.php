<?php

namespace App\Models\Concerns\Tenant;

use App\Models\Employee;
use App\Models\Shop;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Tenant
 */
trait HasRelation
{
    /**
     * @return HasMany<User>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<Employee>
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * @return HasMany<Shop>
     */
    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }
}
