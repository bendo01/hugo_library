<?php

use Bendo01\HugoLibrary\Database\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        $schema = Capsule::schema();
        if ($schema->hasTable('authors')) {
            return; // table already provisioned
        }

        $schema->create('authors', function (Blueprint $table) {
            $this->uuidPrimary($table);
            $table->integer('code');
            $table->string('name', 255);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('modified_by')->nullable();
        });
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('authors');
    }
};
