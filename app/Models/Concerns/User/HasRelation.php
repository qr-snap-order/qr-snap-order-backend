<?php

namespace App\Models\Concerns\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin User
 */
trait HasRelation
{
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class);
    }
}
