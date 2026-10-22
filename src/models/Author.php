<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends BaseModel
{
    protected $table = 'authors';

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'author_id');
    }
}
