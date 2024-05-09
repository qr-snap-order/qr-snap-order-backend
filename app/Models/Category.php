<?php

namespace App\Models;

use App\Models\Concerns\Category\HasRelation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    use HasUuids;
    use HasRelation;

    protected static function boot()
    {
        parent::boot();

        // TODO:: 仕組化する
        self::deleting(
            fn (Category $category) => $category->menuItems()->detach()
        );
    }
}
