<?php

namespace App\Models;

use App\Models\Concerns\ShopGroup\HasRelation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class ShopGroup extends Model
{
    use CascadesDeletes;
    use HasFactory;
    use HasUuids;
    use HasRelation;

    /**
     * @var array<int, string> $cascadeDeletes
     */
    protected array $cascadeDeletes = ['shops'];
}
