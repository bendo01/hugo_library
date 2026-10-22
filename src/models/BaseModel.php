<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $guarded = [];
}
