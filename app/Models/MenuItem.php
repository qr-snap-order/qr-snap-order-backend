<?php

namespace App\Models;

use App\Models\Concerns\MenuItem\HasRelation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

class MenuItem extends Model
{
    use CascadesDeletes;
    use HasFactory;
    use HasUuids;
    use HasRelation;

    protected $touches = ['menuSection'];

    /**
     * @var array<int, string> $cascadeDeletes
     */
    protected array $cascadeDeletes = ['menuItemGroups'];
}
