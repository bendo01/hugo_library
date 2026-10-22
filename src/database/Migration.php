<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

abstract class Migration
{
    abstract public function up(): void;

    abstract public function down(): void;

    /** UUID primary key, defaulting to PostgreSQL 18's uuidv7(). */
    protected function uuidPrimary(Blueprint $table): void
    {
        $column = $table->uuid('id')->primary();
        if (Capsule::connection()->getDriverName() === 'pgsql') {
            $column->default(Capsule::raw('uuidv7()'));
        }
    }
}
