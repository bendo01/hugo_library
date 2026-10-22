<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Controllers;

use Bendo01\HugoLibrary\Models\Author;
use Bendo01\HugoLibrary\Views\AuthorView;
use Bendo01\HugoLibrary\Views\Console;

class AuthorController extends BaseController
{
    public function __construct(private readonly AuthorView $view = new AuthorView())
    {
    }

    protected function title(): string
    {
        return 'Authors';
    }

    protected function actions(): array
    {
        return ['index' => 'List', 'show' => 'Show details'] + parent::actions();
    }

    protected function show(): void
    {
        $author = $this->pick();
        if ($author !== null) {
            $this->view->show($author->load('books.publisher'));
        }
    }

    protected function index(): void
    {
        $this->view->index(Author::withCount('books')->orderBy('name')->get());
    }

    protected function create(): void
    {
        $name = $this->required('Name');
        $code = $name === null ? null : $this->integer('Code');
        if ($name === null || $code === null) {
            return;
        }
        Author::create(['name' => $name, 'code' => $code]);
        Console::success('Author created.');
    }

    protected function edit(): void
    {
        $author = $this->pick();
        if ($author === null) {
            return;
        }
        $name = $this->required('Name', $author->name);
        $code = $name === null ? null : $this->integer('Code', (int) $author->code);
        if ($name === null || $code === null) {
            return;
        }
        $author->update(['name' => $name, 'code' => $code]);
        Console::success('Author updated.');
    }

    protected function delete(): void
    {
        $author = $this->pick();
        if ($author === null) {
            return;
        }
        if ($author->books()->exists()) {
            Console::error('This author still has books; delete those first.');

            return;
        }
        if (Console::confirm("Delete author \"{$author->name}\"?")) {
            $author->delete();
            Console::success('Author deleted.');
        }
    }

    /** Let the user choose an author from a menu. */
    public function pick(string $title = 'Select an author'): ?Author
    {
        $options = Author::orderBy('name')->get()
            ->mapWithKeys(fn (Author $a) => [(string) $a->id => "{$a->name} ({$a->code})"])->all();
        if ($options === []) {
            Console::error('No authors available. Create one first.');

            return null;
        }
        $id = $this->choose($title, $options);

        return $id === null ? null : Author::find($id);
    }
}
