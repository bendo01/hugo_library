# Hugo Library

A terminal (console) CRUD application for managing **authors**, **publishers** and **books**.
Built with PHP, [Laravel Console Menu](https://github.com/nunomaduro/laravel-console-menu),
Eloquent ORM (standalone, without the full Laravel framework) and PostgreSQL (or SQLite), organised as MVC.
Sample data is generated with [Faker PHP](https://github.com/xefi/faker-php) and tests are written with [Pest](https://pestphp.com).

## Features

- Books, Authors and Publishers: list, create, edit and soft delete from an interactive menu
- Books are linked to an author and a publisher
- Migrations (`bin/migrate`) and a Faker-based seeder (`bin/seed`)
- PostgreSQL 18 (UUIDv7 keys) or SQLite

## Requirements

- PHP 8.5+
- [Composer](https://getcomposer.org)
- Either:
  - PostgreSQL 18+ (tables use the `uuidv7()` default) with PHP's `pdo_pgsql` and `pgsql` extensions, **or**
  - SQLite with PHP's `pdo_sqlite` extension (no server; see [Using SQLite](#using-sqlite-instead-of-postgresql))

## Installation – Linux

1. Install PHP, Composer and PostgreSQL and the PHP PostgreSQL driver.
   - Arch / Omarchy: `sudo pacman -S php php-pgsql composer postgresql`
   - Debian / Ubuntu: `sudo apt install php-cli php-pgsql php-xml php-mbstring composer postgresql`
2. Enable the extensions in `php.ini` (Arch: `/etc/php/php.ini`) if not already enabled:
   ```ini
   extension=pdo_pgsql
   extension=pgsql
   ```
   Check with `php -m | grep -i pgsql`.
3. Create the database:
   ```bash
   createdb hugo_library
   ```
4. Install the project:
   ```bash
   git clone <repo-url> hugo_library && cd hugo_library
   composer install
   cp .env.example .env     # then edit DB_USERNAME / DB_PASSWORD
   php bin/migrate
   php bin/seed             # optional: sample data
   php bin/library
   ```

## Installation – Windows

1. Install [PHP for Windows](https://windows.php.net/download/) (Thread Safe or Non-Thread Safe x64), add it to `PATH`.
2. Copy `php.ini-development` to `php.ini` and uncomment:
   ```ini
   extension_dir = "ext"
   extension=pdo_pgsql
   extension=pgsql
   extension=mbstring
   ```
3. Install [Composer](https://getcomposer.org/download/) and [PostgreSQL](https://www.postgresql.org/download/windows/).
   Add PostgreSQL's `bin` folder to `PATH` for `psql`/`createdb`.
4. Create the database (PowerShell): `createdb -U postgres hugo_library`
5. In the project folder:
   ```powershell
   composer install
   Copy-Item .env.example .env   # edit DB_USERNAME / DB_PASSWORD
   php bin/migrate
   php bin/seed              # optional: sample data
   php bin/library
   ```

> The interactive menu needs a real terminal. Use **Windows Terminal** / PowerShell
> (or WSL). Old `cmd.exe` consoles and IDE "run" panes may not support the key handling.

## Configuration

Copy `.env.example` to `.env` and adjust:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hugo_library
DB_USERNAME=bendo01
DB_PASSWORD=
```

### Using SQLite instead of PostgreSQL

No server needed, only PHP's `pdo_sqlite` extension. In `.env` set:

```
DB_CONNECTION=sqlite
DB_DATABASE=database/hugo_library.sqlite
```

The file is created automatically (relative paths are resolved from the project folder);
then run `php bin/migrate` and `php bin/library` as usual. The `pdo_pgsql` extension is not needed.

## Usage

| Command | Purpose |
| --- | --- |
| `php bin/migrate` | Run pending migrations; tables that already exist are left untouched (`composer migrate`) |
| `php bin/migrate rollback` | Roll back the last batch (`composer rollback`) |
| `php bin/seed [authors] [publishers] [books]` | Insert random sample data via Faker (defaults 10 5 30; `composer seed`). Each run adds new rows |
| `php bin/library` | Start the menu (`composer start`) |
| `composer test` | Run the Pest test suite |

Navigate with the arrow keys and Enter. Each entity (Books, Authors, Publishers)
supports List, Create, Edit and Delete. Deletes are soft deletes (`deleted_at`).
An author or publisher that still has books cannot be deleted.

## Testing

Tests use [Pest](https://pestphp.com) and run against an in-memory SQLite database
(no PostgreSQL needed):

```bash
composer test      # or: vendor/bin/pest
```

Tests need `pdo_sqlite` only. Install dev dependencies with `composer install` (the default).

## Project structure

```
bin/                  entry scripts (library, migrate, seed)
database/migrations/  migration files
database/seeders/     sample data seeder
src/models/           Eloquent models (Author, Publisher, Book)
src/views/            Table/message rendering
src/controllers/      menu flow and CRUD logic
src/database/         DB bootstrap and migration runner
tests/                Pest tests
```

Note: the original `publishers.modied_by` column typo is renamed to `modified_by` by the migration.
