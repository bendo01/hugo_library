<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Controllers;

use Bendo01\HugoLibrary\Models\Publisher;
use Bendo01\HugoLibrary\Views\PublisherView;
use Bendo01\HugoLibrary\Views\Console;

class PublisherController extends BaseController
{
    public function __construct(private readonly PublisherView $view = new PublisherView())
    {
    }

    protected function title(): string
    {
        return 'Publishers';
    }

    protected function index(): void
    {
        $this->view->index(Publisher::withCount('books')->orderBy('name')->get());
    }

    protected function create(): void
    {
        $name = $this->required('Name');
        $code = $name === null ? null : $this->integer('Code');
        if ($name === null || $code === null) {
            return;
        }
        Publisher::create(['name' => $name, 'code' => $code]);
        Console::success('Publisher created.');
    }

    protected function edit(): void
    {
        $publisher = $this->pick();
        if ($publisher === null) {
            return;
        }
        $name = $this->required('Name', $publisher->name);
        $code = $name === null ? null : $this->integer('Code', (int) $publisher->code);
        if ($name === null || $code === null) {
            return;
        }
        $publisher->update(['name' => $name, 'code' => $code]);
        Console::success('Publisher updated.');
    }

    protected function delete(): void
    {
        $publisher = $this->pick();
        if ($publisher === null) {
            return;
        }
        if ($publisher->books()->exists()) {
            Console::error('This publisher still has books; delete those first.');

            return;
        }
        if (Console::confirm("Delete publisher \"{$publisher->name}\"?")) {
            $publisher->delete();
            Console::success('Publisher deleted.');
        }
    }

    /** Let the user choose a publisher from a menu. */
    public function pick(string $title = 'Select a publisher'): ?Publisher
    {
        $options = Publisher::orderBy('name')->get()
            ->mapWithKeys(fn (Publisher $a) => [(string) $a->id => "{$a->name} ({$a->code})"])->all();
        if ($options === []) {
            Console::error('No publishers available. Create one first.');

            return null;
        }
        $id = $this->choose($title, $options);

        return $id === null ? null : Publisher::find($id);
    }
}
