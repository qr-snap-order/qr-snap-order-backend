<?php

namespace App\Models;

use App\Models\Concerns\MenuSection\HasRelation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class MenuSection extends Model
{
    use CascadesDeletes;
    use HasFactory;
    use HasUuids;
    use HasRelation;

    protected $touches = ['menu'];

    /**
     * @var array<int, string> $cascadeDeletes
     */
    protected array $cascadeDeletes = ['menuItems'];
}
