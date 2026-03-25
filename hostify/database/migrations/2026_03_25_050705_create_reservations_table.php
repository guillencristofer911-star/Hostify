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
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('guest_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('room_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->index('created_by');
            $table->enum('source', ['web_form', 'manual_reception'])->default('manual_reception');
            $table->enum('status', ['pendiente', 'aprobada', 'rechazada', 'activa', 'checked_out', 'cancelada'])->default('pendiente');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->timestamp('actual_check_in')->nullable();
            $table->timestamp('actual_check_out')->nullable();
            $table->decimal('rate', 10, 2);
            $table->string('pre_register_token', 64)->nullable()->unique();
            $table->text('rejection_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'check_in_date']);
            $table->index('pre_register_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
