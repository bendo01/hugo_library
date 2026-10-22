<?php

use Bendo01\HugoLibrary\Database\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        $schema = Capsule::schema();
        if ($schema->hasTable('publishers')) {
            // earlier schema had the typo "modied_by"
            if ($schema->hasColumn('publishers', 'modied_by') && ! $schema->hasColumn('publishers', 'modified_by')) {
                $schema->table('publishers', fn (Blueprint $t) => $t->renameColumn('modied_by', 'modified_by'));
            }

            return;
        }

        $schema->create('publishers', function (Blueprint $table) {
            $this->uuidPrimary($table);
            $table->integer('code')->default(0);
            $table->string('name', 255);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('modified_by')->nullable();
        });
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('publishers');
    }
};
