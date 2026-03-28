<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camarera_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('room_id')->constrained()->onDelete('cascade');
            $table->date('assigned_date');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['user_id', 'room_id', 'assigned_date']);
            $table->index(['assigned_date', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camarera_assignments');
    }
};