<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

            // Status: string sin default aquí — se aplica después del CAST al tipo nativo
            $table->string('status', 20)->nullable();

            // Pre-asignación
            $table->date('assigned_date');

            // Ejecución real
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->integer('duration_minutes')->nullable();

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

        // 1. Convertir la columna al tipo nativo de PostgreSQL
        //    (sin default activo — la columna es nullable en este punto)
        DB::statement("
            ALTER TABLE cleaning_sessions
            ALTER COLUMN status TYPE cleaning_status
            USING status::cleaning_status
        ");

        // 2. Ahora que el tipo es correcto, aplicar el default nativo
        DB::statement("
            ALTER TABLE cleaning_sessions
            ALTER COLUMN status SET DEFAULT 'pendiente'::cleaning_status
        ");

        // 3. Ahora que hay default, quitar nullable
        DB::statement("
            ALTER TABLE cleaning_sessions
            ALTER COLUMN status SET NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaning_sessions');
    }
};