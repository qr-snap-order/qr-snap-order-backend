<?php

namespace App\Models;

use App\Models\Concerns\Staff\HasRelation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;
    use HasUuids;
    use HasRelation;
}
