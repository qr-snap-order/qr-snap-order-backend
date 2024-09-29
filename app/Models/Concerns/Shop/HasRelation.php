<?php

namespace App\Models\Concerns\Shop;

use App\Models\Employee;
use App\Models\Shop;
use App\Models\ShopGroup;
use App\Models\ShopGroupAssignment;
use App\Models\ShopTable;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Shop
 */
trait HasRelation
{
    /**
     * @return BelongsTo<Tenant, Shop>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return BelongsToMany<Employee>
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'shop_employee');
    }

    /**
     * @return BelongsToMany<ShopGroup>
     */
    public function shopGroups(): BelongsToMany
    {
        return $this->belongsToMany(ShopGroup::class, 'shop_group_assignments')->using(ShopGroupAssignment::class);
    }

    /**
     * @return HasMany<ShopTable>
     */
    public function shopTables(): HasMany
    {
        return $this->hasMany(ShopTable::class);
    }
}
