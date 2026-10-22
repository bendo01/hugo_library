<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Controllers;

use Bendo01\HugoLibrary\Models\Book;
use Bendo01\HugoLibrary\Views\BookView;
use Bendo01\HugoLibrary\Views\Console;

class BookController extends BaseController
{
    public function __construct(
        private readonly BookView $view = new BookView(),
        private readonly AuthorController $authors = new AuthorController(),
        private readonly PublisherController $publishers = new PublisherController(),
    ) {
    }

    protected function title(): string
    {
        return 'Books';
    }

    protected function index(): void
    {
        $this->view->index(Book::with(['author', 'publisher'])->orderBy('title')->get());
    }

    protected function create(): void
    {
        $title = $this->required('Title');
        $year = $title === null ? null : $this->integer('Year', 0);
        if ($title === null || $year === null) {
            return;
        }
        $author = $this->authors->pick();
        $publisher = $author === null ? null : $this->publishers->pick();
        if ($author === null || $publisher === null) {
            Console::error('A book needs an author and a publisher.');

            return;
        }
        Book::create([
            'title' => $title,
            'book_year' => $year,
            'author_id' => $author->id,
            'publisher_id' => $publisher->id,
        ]);
        Console::success('Book created.');
    }

    protected function edit(): void
    {
        $book = $this->pick();
        if ($book === null) {
            return;
        }
        $title = $this->required('Title', $book->title);
        $year = $title === null ? null : $this->integer('Year', (int) $book->book_year);
        if ($title === null || $year === null) {
            return;
        }
        $data = ['title' => $title, 'book_year' => $year];

        if (Console::confirm('Change author?') && ($author = $this->authors->pick()) !== null) {
            $data['author_id'] = $author->id;
        }
        if (Console::confirm('Change publisher?') && ($publisher = $this->publishers->pick()) !== null) {
            $data['publisher_id'] = $publisher->id;
        }
        $book->update($data);
        Console::success('Book updated.');
    }

    protected function delete(): void
    {
        $book = $this->pick();
        if ($book !== null && Console::confirm("Delete book \"{$book->title}\"?")) {
            $book->delete();
            Console::success('Book deleted.');
        }
    }

    private function pick(): ?Book
    {
        $options = Book::orderBy('title')->get()
            ->mapWithKeys(fn (Book $b) => [(string) $b->id => "{$b->title} ({$b->book_year})"])->all();
        if ($options === []) {
            Console::error('No books available. Create one first.');

            return null;
        }
        $id = $this->choose('Select a book', $options);

        return $id === null ? null : Book::find($id);
    }
}
