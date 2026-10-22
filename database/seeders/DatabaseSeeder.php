<?php

declare(strict_types=1);

namespace Bendo01\HugoLibrary\Seeders;

use Bendo01\HugoLibrary\Models\Author;
use Bendo01\HugoLibrary\Models\Book;
use Bendo01\HugoLibrary\Models\Publisher;
use Xefi\Faker\Faker;

/** Inserts random sample data generated with xefi/faker-php. */
class DatabaseSeeder
{
    private readonly Faker $faker;

    public function __construct(
        private readonly int $authors = 10,
        private readonly int $publishers = 5,
        private readonly int $books = 30,
    ) {
        $this->faker = new Faker('en_US');
    }

    /** @return array{authors:int,publishers:int,books:int} number of rows created */
    public function run(): array
    {
        $authorCode = (int) Author::withTrashed()->max('code');
        for ($i = 0; $i < $this->authors; $i++) {
            Author::create(['code' => ++$authorCode, 'name' => $this->faker->name()]);
        }

        $publisherCode = (int) Publisher::withTrashed()->max('code');
        for ($i = 0; $i < $this->publishers; $i++) {
            Publisher::create(['code' => ++$publisherCode, 'name' => $this->faker->company()]);
        }

        // Books may reference rows from earlier runs as well.
        $authors = Author::all()->all();
        $publishers = Publisher::all()->all();

        $createdBooks = 0;
        if ($authors !== [] && $publishers !== []) {
            for ($i = 0; $i < $this->books; $i++) {
                Book::create([
                    'title' => ucwords($this->faker->words(random_int(2, 5))),
                    'book_year' => $this->faker->number(1950, (int) date('Y')),
                    'author_id' => $this->faker->randomElement($authors)->id,
                    'publisher_id' => $this->faker->randomElement($publishers)->id,
                ]);
                $createdBooks++;
            }
        }

        return ['authors' => $this->authors, 'publishers' => $this->publishers, 'books' => $createdBooks];
    }
}
