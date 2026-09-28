<?php

use App\Enums\PortfolioStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('thumbnail');
            $table->string('name');
            $table->string('category')->index();
            $table->text('description');
            $table->string('demo_link')->nullable();
            $table->string('repository_link');
            $table->string('status')->default(PortfolioStatus::DRAFT->value)->index();
            $table->json('tech_stacks')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['status', 'category']);
        });

        Schema::create('portfolio_galery', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('portfolio_id')->constrained('portfolio')->cascadeOnDelete();
            $table->string('image_url');
            $table->timestamp('created_at')->nullable();

            $table->index('portfolio_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_galery');
        Schema::dropIfExists('portfolio');
    }
};
