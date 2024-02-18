<?php

namespace App\Models\Concerns\Shop;

use App\Models\Employee;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin Shop
 */
trait HasRelation
{
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class);
    }
}
