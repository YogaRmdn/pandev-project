<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The portfolio form treats the repository link as optional, but the column
     * was created NOT NULL, so a save without it blew up at the database layer
     * instead of failing validation.
     */
    public function up(): void
    {
        Schema::table('portfolio', function (Blueprint $table) {
            $table->string('repository_link')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('portfolio', function (Blueprint $table) {
            $table->string('repository_link')->nullable(false)->change();
        });
    }
};
