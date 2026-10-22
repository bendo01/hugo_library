<?php

declare(strict_types=1);

use Bendo01\HugoLibrary\Database\Migrator;
use Illuminate\Database\Capsule\Manager as Capsule;

$path = dirname(__DIR__, 2) . '/database/migrations';

it('creates all tables', function () {
    foreach (['authors', 'publishers', 'books', 'migrations'] as $table) {
        expect(Capsule::schema()->hasTable($table))->toBeTrue();
    }
});

it('has the expected columns', function () {
    expect(Capsule::schema()->getColumnListing('publishers'))
        ->toContain('modified_by', 'deleted_at', 'code');
    expect(Capsule::schema()->getColumnListing('books'))
        ->toContain('title', 'book_year', 'author_id', 'publisher_id');
});

it('does not run migrations twice', function () use ($path) {
    expect((new Migrator($path))->migrate())->toBe([]);
});

it('rolls back the last batch', function () use ($path) {
    $rolledBack = (new Migrator($path))->rollback();

    expect($rolledBack)->toHaveCount(3);
    expect(Capsule::schema()->hasTable('books'))->toBeFalse();
});
