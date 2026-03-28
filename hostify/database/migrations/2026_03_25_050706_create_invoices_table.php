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
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('reservation_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number', 20)->unique();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('taxes', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['borrador', 'emitida', 'pagada', 'anulada'])->default('borrador');
            $table->timestamp('sent_at')->nullable();
            $table->string('sent_to_email', 150)->nullable();
            $table->timestamps();

            $table->unique('reservation_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
