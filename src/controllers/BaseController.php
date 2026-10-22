<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Controllers;

use Bendo01\HugoLibrary\Views\Console;
use NunoMaduro\LaravelConsoleMenu\Menu;

abstract class BaseController
{
    /** Title of the CRUD sub menu. */
    abstract protected function title(): string;

    abstract protected function index(): void;

    abstract protected function create(): void;

    abstract protected function edit(): void;

    abstract protected function delete(): void;

    /** @return array<string,string> method name => menu label */
    protected function actions(): array
    {
        return [
            'index' => 'List',
            'create' => 'Create',
            'edit' => 'Edit',
            'delete' => 'Delete',
        ];
    }

    public function run(): void
    {
        $actions = $this->actions();

        while (($choice = $this->choose($this->title(), $actions)) !== null) {
            $this->{$choice}();
            Console::pause();
        }
    }

    /**
     * Show a menu and return the selected key, or null when the user exits.
     *
     * @param array<string,string> $options
     */
    protected function choose(string $title, array $options): ?string
    {
        if ($options === []) {
            return null;
        }
        $result = (new Menu($title))
            ->setExitButtonText('Back')
            ->addOptions($options)
            ->open();

        return $result === null ? null : (string) $result;
    }

    /** Ask for a required text value; returns null when left empty. */
    protected function required(string $label, ?string $default = null): ?string
    {
        $value = Console::ask($label, $default);
        if ($value === null || $value === '') {
            Console::error("{$label} is required.");

            return null;
        }

        return $value;
    }

    /** Ask for an integer value; returns null (and reports) if invalid. */
    protected function integer(string $label, ?int $default = null): ?int
    {
        $value = Console::ask($label, $default === null ? null : (string) $default);
        if ($value === null || ! preg_match('/^-?\d+$/', $value)) {
            Console::error("{$label} must be a whole number.");

            return null;
        }

        return (int) $value;
    }
}
