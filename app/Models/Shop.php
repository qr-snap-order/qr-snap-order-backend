<?php

namespace App\Models;

use App\Models\Concerns\Shop\HasRelation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    use HasUuids;
    use HasRelation;

    protected static function boot()
    {
        parent::boot();

        // TODO:: 仕組化する
        self::deleting(
            fn (Shop $shop) => $shop->employees()->detach()
        );
    }
}
