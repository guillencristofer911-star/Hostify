<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('reservation_id')->constrained()->onDelete('cascade');
            $table->foreignId('registered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('shift_close_id')->nullable()->constrained('shift_closes')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['efectivo', 'datafono']);
            $table->timestamp('paid_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['reservation_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
