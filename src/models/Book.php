<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends BaseModel
{
    protected $table = 'books';

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'author_id');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class, 'publisher_id');
    }
}
