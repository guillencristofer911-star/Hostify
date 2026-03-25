<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // DROP IF EXISTS primero → evita error "ya existe"
        DB::statement('DROP TYPE IF EXISTS room_status CASCADE');
        DB::statement('DROP TYPE IF EXISTS reservation_status CASCADE');
        DB::statement('DROP TYPE IF EXISTS cleaning_status CASCADE');
        DB::statement('DROP TYPE IF EXISTS shift_status CASCADE');
        DB::statement('DROP TYPE IF EXISTS incident_status CASCADE');
        DB::statement('DROP TYPE IF EXISTS incident_category CASCADE');
        DB::statement('DROP TYPE IF EXISTS reservation_source CASCADE');
        DB::statement('DROP TYPE IF EXISTS payment_method CASCADE');
        DB::statement('DROP TYPE IF EXISTS invoice_status CASCADE');

        // Crear ENUMs limpios
        DB::statement("CREATE TYPE room_status AS ENUM ('libre', 'sucia', 'ocupada', 'no_disponible')");
        DB::statement("CREATE TYPE reservation_status AS ENUM ('pendiente', 'aprobada', 'rechazada', 'activa', 'checked_out', 'cancelada')");
        DB::statement("CREATE TYPE cleaning_status AS ENUM ('pendiente', 'en_proceso', 'terminada')");
        DB::statement("CREATE TYPE shift_status AS ENUM ('abierto', 'cerrado', 'validado')");
        DB::statement("CREATE TYPE incident_status AS ENUM ('pendiente', 'en_proceso', 'resuelto')");
        DB::statement("CREATE TYPE incident_category AS ENUM ('mantenimiento', 'huesped', 'administrativo', 'objetos_perdidos')");
        DB::statement("CREATE TYPE reservation_source AS ENUM ('web_form', 'manual_reception')");
        DB::statement("CREATE TYPE payment_method AS ENUM ('efectivo', 'datafono')");
        DB::statement("CREATE TYPE invoice_status AS ENUM ('borrador', 'emitida', 'anulada')");
    }

    public function down(): void
    {
        DB::statement('DROP TYPE IF EXISTS room_status CASCADE');
        DB::statement('DROP TYPE IF EXISTS reservation_status CASCADE');
        DB::statement('DROP TYPE IF EXISTS cleaning_status CASCADE');
        DB::statement('DROP TYPE IF EXISTS shift_status CASCADE');
        DB::statement('DROP TYPE IF EXISTS incident_status CASCADE');
        DB::statement('DROP TYPE IF EXISTS incident_category CASCADE');
        DB::statement('DROP TYPE IF EXISTS reservation_source CASCADE');
        DB::statement('DROP TYPE IF EXISTS payment_method CASCADE');
        DB::statement('DROP TYPE IF EXISTS invoice_status CASCADE');
    }
};
