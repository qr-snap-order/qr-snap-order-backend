<?php

namespace App\Models;

use App\Models\Concerns\Category\HasRelation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class Category extends Model
{
    use CascadesDeletes;
    use HasFactory;
    use HasUuids;
    use HasRelation;

    /**
     * @var array<int, string> $cascadeDeletes
     */
    protected array $cascadeDeletes = ['menuItems'];
}
