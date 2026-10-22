<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

final class Migrator
{
    public function __construct(private readonly string $path)
    {
    }

    /** @return string[] names of migrations that were run */
    public function migrate(): array
    {
        $this->ensureRepository();
        $ran = Capsule::table('migrations')->pluck('migration')->all();
        $batch = (int) Capsule::table('migrations')->max('batch') + 1;
        $done = [];

        foreach ($this->files() as $name => $file) {
            if (in_array($name, $ran, true)) {
                continue;
            }
            $this->resolve($file)->up();
            Capsule::table('migrations')->insert(['migration' => $name, 'batch' => $batch]);
            $done[] = $name;
        }

        return $done;
    }

    /** @return string[] names of migrations that were rolled back (last batch) */
    public function rollback(): array
    {
        $this->ensureRepository();
        $batch = (int) Capsule::table('migrations')->max('batch');
        $files = $this->files();
        $done = [];

        $rows = Capsule::table('migrations')->where('batch', $batch)->orderByDesc('id')->pluck('migration');
        foreach ($rows as $name) {
            if (isset($files[$name])) {
                $this->resolve($files[$name])->down();
            }
            Capsule::table('migrations')->where('migration', $name)->delete();
            $done[] = $name;
        }

        return $done;
    }

    private function ensureRepository(): void
    {
        $schema = Capsule::schema();
        if (! $schema->hasTable('migrations')) {
            $schema->create('migrations', function ($table) {
                $table->increments('id');
                $table->string('migration');
                $table->integer('batch');
            });
        }
    }

    /** @return array<string,string> */
    private function files(): array
    {
        $files = glob($this->path . '/*.php') ?: [];
        sort($files);

        return array_combine(array_map(fn (string $f) => basename($f, '.php'), $files), $files);
    }

    private function resolve(string $file): Migration
    {
        return require $file;
    }
}
