<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_checks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('cleaning_session_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->unsignedSmallInteger('expected_quantity');
            $table->unsignedSmallInteger('quantity_found');
            $table->boolean('is_ok');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['cleaning_session_id', 'item_id']);
            $table->index('cleaning_session_id');
            $table->index('is_ok');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_checks');
    }
};