<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('room_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('reservation_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignUuid('cleaning_session_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');

            $table->string('title', 150);
            $table->enum('category', ['mantenimiento', 'huesped', 'administrativo', 'objetos_perdidos']);
            $table->enum('priority', ['baja', 'media', 'alta'])->default('media');
            $table->enum('status', ['pendiente', 'en_proceso', 'resuelto'])->default('pendiente');

            $table->text('description')->nullable();
            $table->string('photo_url', 500)->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['status', 'category']);
            $table->index('room_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};