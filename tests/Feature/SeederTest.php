<?php

declare(strict_types=1);

use Bendo01\HugoLibrary\Models\Author;
use Bendo01\HugoLibrary\Models\Book;
use Bendo01\HugoLibrary\Models\Publisher;
use Bendo01\HugoLibrary\Seeders\DatabaseSeeder;

it('seeds the requested number of rows', function () {
    expect((new DatabaseSeeder(3, 2, 8))->run())
        ->toBe(['authors' => 3, 'publishers' => 2, 'books' => 8]);

    expect(Author::count())->toBe(3)
        ->and(Publisher::count())->toBe(2)
        ->and(Book::count())->toBe(8);
});

it('generates valid, related data', function () {
    (new DatabaseSeeder(2, 2, 5))->run();

    Book::with(['author', 'publisher'])->get()->each(function (Book $book) {
        expect($book->title)->not->toBeEmpty()
            ->and($book->book_year)->toBeGreaterThanOrEqual(1950)
            ->and($book->author)->not->toBeNull()
            ->and($book->publisher)->not->toBeNull();
    });
});

it('assigns unique codes across runs', function () {
    (new DatabaseSeeder(3, 3, 1))->run();
    (new DatabaseSeeder(3, 3, 1))->run();

    expect(Author::pluck('code')->unique())->toHaveCount(6)
        ->and(Publisher::pluck('code')->unique())->toHaveCount(6);
});
