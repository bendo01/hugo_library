<?php

declare(strict_types=1);

use Bendo01\HugoLibrary\Models\Author;
use Bendo01\HugoLibrary\Models\Book;
use Bendo01\HugoLibrary\Models\Publisher;

function makeBook(string $title = 'The Hobbit'): Book
{
    $author = Author::create(['name' => 'Tolkien', 'code' => 1]);
    $publisher = Publisher::create(['name' => 'Allen & Unwin', 'code' => 2]);

    return Book::create([
        'title' => $title,
        'book_year' => 1937,
        'author_id' => $author->id,
        'publisher_id' => $publisher->id,
    ]);
}

it('generates a uuid primary key', function () {
    $author = Author::create(['name' => 'Tolkien', 'code' => 1]);

    expect($author->id)->toBeString()->toMatch('/^[0-9a-f-]{36}$/');
});

it('links books to authors and publishers', function () {
    $book = makeBook();

    expect($book->author->name)->toBe('Tolkien')
        ->and($book->publisher->name)->toBe('Allen & Unwin')
        ->and($book->author->books)->toHaveCount(1)
        ->and($book->publisher->books)->toHaveCount(1);
});

it('updates records', function () {
    $book = makeBook();
    $book->update(['title' => 'The Silmarillion', 'book_year' => 1977]);

    expect(Book::find($book->id))
        ->title->toBe('The Silmarillion')
        ->book_year->toBe(1977);
});

it('soft deletes records', function () {
    $book = makeBook();
    $book->delete();

    expect(Book::count())->toBe(0)
        ->and(Book::withTrashed()->count())->toBe(1)
        ->and(Book::withTrashed()->first()->deleted_at)->not->toBeNull();
});

it('counts books per author', function () {
    $book = makeBook();
    Book::create([
        'title' => 'LOTR',
        'book_year' => 1954,
        'author_id' => $book->author_id,
        'publisher_id' => $book->publisher_id,
    ]);

    expect(Author::withCount('books')->first()->books_count)->toBe(2);
});
