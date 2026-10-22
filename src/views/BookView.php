<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Views;

use Illuminate\Support\Collection;

class BookView
{
    public function index(Collection $books): void
    {
        if ($books->isEmpty()) {
            Console::info('No books yet.');

            return;
        }
        Console::table(
            ['#', 'Title', 'Year', 'Author', 'Publisher'],
            $books->values()->map(fn ($b, $i) => [
                $i + 1,
                $b->title,
                $b->book_year,
                $b->author?->name ?? '-',
                $b->publisher?->name ?? '-',
            ])->all(),
            'Books'
        );
    }
}
