<?php

use App\Enums\InvoiceStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('description');
            $table->date('date')->index();
            $table->string('status')->default(InvoiceStatus::UNPAID->value)->index();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 15, 2);
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type')->default(TransactionType::INCOME->value)->index();
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->date('date')->index();
            // Link auto-generated income rows to their invoice so a status
            // change book-keeps the delta instead of duplicating the amount.
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('transactions');
    }
};
