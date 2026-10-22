<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Views;

use Illuminate\Support\Collection;

class AuthorView
{
    public function index(Collection $authors): void
    {
        if ($authors->isEmpty()) {
            Console::info('No authors yet.');

            return;
        }
        Console::table(
            ['#', 'Code', 'Name', 'Books'],
            $authors->values()->map(fn ($a, $i) => [$i + 1, $a->code, $a->name, $a->books_count ?? 0])->all(),
            'Authors'
        );
    }
}
