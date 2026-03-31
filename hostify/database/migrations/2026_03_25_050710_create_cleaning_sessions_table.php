<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleaning_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relaciones
            $table->foreignUuid('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('reservation_id')->nullable()->constrained()->onDelete('set null');

            // Estado del flujo: asignada → en_proceso → terminada → verificada
            $table->enum('status', ['asignada', 'en_proceso', 'terminada', 'verificada'])->default('asignada');

            // Pre-asignación (reemplaza camarera_assignments)
            $table->date('assigned_date');          // Fecha para la que fue asignada

            // Ejecución real
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->integer('duration_minutes')->nullable(); // Se calcula en app al cerrar

            // Evidencia y notas
            $table->string('photo_after_url', 500)->nullable();
            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Índices
            $table->index(['room_id', 'status']);
            $table->index(['assigned_to', 'assigned_date']);
            $table->index('assigned_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaning_sessions');
    }
};