<?php

use Bendo01\HugoLibrary\Database\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        $schema = Capsule::schema();
        if ($schema->hasTable('books')) {
            return; // table already provisioned
        }

        $schema->create('books', function (Blueprint $table) {
            $this->uuidPrimary($table);
            $table->string('title', 255);
            $table->integer('book_year')->default(0);
            $table->foreignUuid('publisher_id')->constrained('publishers');
            $table->foreignUuid('author_id')->constrained('authors');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('modified_by')->nullable();
        });
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('books');
    }
};
