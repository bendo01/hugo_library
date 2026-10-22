<?php

declare(strict_types=1);

use Bendo01\HugoLibrary\Database\Database;
use Bendo01\HugoLibrary\Database\Migrator;

/*
 * Every test runs against a fresh in-memory SQLite database
 * with all migrations applied, so PostgreSQL is not required.
 */
pest()->beforeEach(function () {
    foreach (['DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => ':memory:'] as $key => $value) {
        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }

    $base = dirname(__DIR__);
    Database::boot($base);
    (new Migrator($base . '/database/migrations'))->migrate();
})->in('Feature');
