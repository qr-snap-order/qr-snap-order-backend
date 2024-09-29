<?php

namespace App\Models;

use App\Models\Concerns\Order\HasRelation;
use App\Models\Concerns\Order\HasScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Order extends Model
{
    use CascadesDeletes;
    use HasFactory;
    use HasUuids;
    use HasRelation;
    use HasScope;

    /**
     * @var array<int, string> $cascadeDeletes
     */
    protected array $cascadeDeletes = ['orderItems'];
}
