<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Views;

use Bendo01\HugoLibrary\Models\Author;
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

    /** Author details followed by the author's books (books and publisher relations loaded). */
    public function show(Author $author): void
    {
        Console::table(
            ['Field', 'Value'],
            [
                ['Name', $author->name],
                ['Code', $author->code],
                ['Books', $author->books->count()],
                ['Created', (string) $author->created_at],
                ['Updated', (string) $author->updated_at],
            ],
            'Author'
        );

        if ($author->books->isEmpty()) {
            Console::info('This author has no books.');

            return;
        }
        Console::table(
            ['#', 'Title', 'Year', 'Publisher'],
            $author->books->sortBy('title')->values()->map(fn ($b, $i) => [
                $i + 1,
                $b->title,
                $b->book_year,
                $b->publisher?->name ?? '-',
            ])->all(),
            "Books by {$author->name}"
        );
    }
}
