<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Publisher extends BaseModel
{
    protected $table = 'publishers';

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'publisher_id');
    }
}
