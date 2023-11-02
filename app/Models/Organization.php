<?php

namespace App\Models;

use App\Models\Concerns\Organization\HasRelation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    use HasUuids;
    use HasRelation;
}
