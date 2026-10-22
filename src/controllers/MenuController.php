<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Controllers;

class MenuController extends BaseController
{
    protected function title(): string
    {
        return 'Hugo Library';
    }

    public function run(): void
    {
        $screens = [
            'books' => ['Books', BookController::class],
            'authors' => ['Authors', AuthorController::class],
            'publishers' => ['Publishers', PublisherController::class],
        ];
        $options = array_map(fn ($s) => $s[0], $screens);

        while (($choice = $this->choose($this->title(), $options)) !== null) {
            (new $screens[$choice][1]())->run();
        }
    }

    protected function index(): void
    {
    }

    protected function create(): void
    {
    }

    protected function edit(): void
    {
    }

    protected function delete(): void
    {
    }
}
