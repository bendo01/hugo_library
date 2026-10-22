<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Database;

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;

final class Database
{
    public static function boot(string $basePath): Capsule
    {
        if (is_file($basePath . '/.env')) {
            Dotenv::createImmutable($basePath)->safeLoad();
        }

        $driver = self::env('DB_CONNECTION', 'pgsql');

        $config = $driver === 'sqlite'
            ? ['driver' => 'sqlite', 'database' => self::sqlitePath($basePath), 'prefix' => '', 'foreign_key_constraints' => true]
            : [
                'driver' => 'pgsql',
                'host' => self::env('DB_HOST', '127.0.0.1'),
                'port' => self::env('DB_PORT', '5432'),
                'database' => self::env('DB_DATABASE', 'hugo_library'),
                'username' => self::env('DB_USERNAME', 'postgres'),
                'password' => self::env('DB_PASSWORD', ''),
                'charset' => 'utf8',
                'prefix' => '',
                'search_path' => 'public',
                'sslmode' => 'prefer',
            ];

        $capsule = new Capsule();
        $capsule->addConnection($config);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule;
    }

    /** Resolve the SQLite file (relative to the project) and create it if missing. */
    private static function sqlitePath(string $basePath): string
    {
        $path = self::env('DB_DATABASE', 'database/hugo_library.sqlite');
        if ($path === ':memory:') {
            return $path;
        }
        // Treat "hugo_library" (the PostgreSQL default) as no explicit file.
        if (! preg_match('#(\.sqlite3?|\.db)$#', $path)) {
            $path = 'database/hugo_library.sqlite';
        }
        if (! preg_match('#^([a-zA-Z]:)?[\\/]#', $path)) {
            $path = $basePath . '/' . $path;
        }
        if (! is_file($path)) {
            touch($path);
        }

        return $path;
    }

    private static function env(string $key, string $default): string
    {
        return ($_ENV[$key] ?? getenv($key)) |> (fn ($v) => $v === false ? $default : (string) $v);
    }
}
