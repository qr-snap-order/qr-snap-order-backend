<?php

namespace App\Models\Concerns\User;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin User
 */
trait HasRelation
{
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
