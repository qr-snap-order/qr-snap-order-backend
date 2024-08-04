<?php

namespace App\Models\Concerns\MenuItem;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @mixin MenuItem
 */
trait HasAttributes
{
    /**
     * @return Attribute<?string, never>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(
            fn (): ?string =>  $this->image ? config('image.public_url') . '/' . $this->image : null,
        );
    }
}
