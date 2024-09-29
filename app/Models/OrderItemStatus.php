<?php

namespace App\Models;

use App\Models\Concerns\OrderItemStatus\HasRelation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;


class OrderItemStatus extends Model
{
    use CascadesDeletes;
    use HasFactory;
    use HasUuids;
    use HasRelation;
}
