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
        Schema::create('shift_closes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('opened_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('shift_start');
            $table->timestamp('shift_end')->nullable();
            $table->decimal('total_cash_system', 10, 2)->default(0);
            $table->decimal('total_card_system', 10, 2)->default(0);
            $table->decimal('total_cash_counted', 10, 2)->nullable();
            $table->decimal('difference', 10, 2)->nullable();
            $table->boolean('within_margin')->nullable();
            $table->decimal('margin_threshold', 10, 2)->nullable();
            $table->text('observations')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->text('digital_signature')->nullable();
            $table->enum('status', ['abierto', 'cerrado', 'validado'])->default('abierto');
            $table->timestamps();

            $table->index(['opened_by', 'shift_start']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_closes');
    }
};
