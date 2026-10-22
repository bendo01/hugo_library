<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Views;

use Illuminate\Support\Collection;

class PublisherView
{
    public function index(Collection $publishers): void
    {
        if ($publishers->isEmpty()) {
            Console::info('No publishers yet.');

            return;
        }
        Console::table(
            ['#', 'Code', 'Name', 'Books'],
            $publishers->values()->map(fn ($p, $i) => [$i + 1, $p->code, $p->name, $p->books_count ?? 0])->all(),
            'Publishers'
        );
    }
}
