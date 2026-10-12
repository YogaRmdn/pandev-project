<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            return;
        }

        // Existing databases created the role column with an index
        // (users_role_index). SQLite refuses to drop a column that an index
        // still references, so the index has to go first — otherwise the drop
        // fails with "error in index users_role_index after drop column".
        $hasIndex = collect(Schema::getIndexes('users'))
            ->contains(fn (array $index) => $index['name'] === 'users_role_index');

        Schema::table('users', function (Blueprint $table) use ($hasIndex) {
            if ($hasIndex) {
                $table->dropIndex('users_role_index');
            }

            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('ADMIN');
        });
    }
};
