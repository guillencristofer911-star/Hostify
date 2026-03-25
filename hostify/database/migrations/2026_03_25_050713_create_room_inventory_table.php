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
        Schema::create('room_inventory', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('room_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->smallInteger('current_quantity')->default(0);
            $table->timestamps();

            $table->unique(['room_id', 'item_id']);
            $table->index(['room_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_inventory');
    }
};
