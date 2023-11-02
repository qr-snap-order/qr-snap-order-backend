<?php

namespace App\Models\Concerns\Organization;

use App\Models\Organization;
use App\Models\Shop;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Organization
 */
trait HasRelation
{
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function staffs(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }
}
