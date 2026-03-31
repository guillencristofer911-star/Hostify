<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_inventory', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('room_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->unsignedSmallInteger('expected_quantity')->default(1);
            $table->unsignedSmallInteger('current_quantity')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['room_id', 'item_id']);
            $table->index(['room_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_inventory');
    }
};